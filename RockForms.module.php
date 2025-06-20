<?php

namespace ProcessWire;

use Nette\Forms\Control;
use Nette\Forms\FormRenderer;
use Nette\Forms\Validator;
use Nette\Http\Helpers;
use RockForms\Entries;
use RockForms\Entry;
use RockForms\Renderer\RockFormsRenderer;
use RockForms\RockForm;
use RockForms\RockMigrationsConstants;
use RockForms\Root;

/**
 * @author Bernhard Baumrock, 07.03.2023
 * @license COMMERCIAL PLEASE DO NOT DISTRIBUTE
 * @link https://www.baumrock.com
 */

function rockforms(): RockForms
{
  return wire()->modules->get('RockForms');
}

// load nette autoloader even before the rockforms class
// this is necessary for fileuploads to work properly
require_once __DIR__ . "/vendor/autoload.php";

class RockForms extends WireData implements Module, ConfigurableModule
{
  use RockMigrationsConstants;

  const csrfstring = "rockforms-csrf";

  public $confirmParam = "forms-confirm";

  public $honeypotfields = "";

  /** @var WireArray */
  public $rendered;

  /** @var WireData */
  public $renderedMarkup;

  public $submitCount;

  public $successParam = false;

  public $submitdelay = 0;

  private $forms;

  private $formsWireDataObject;

  public function init()
  {

    // set netteforms _nss cookie to 1 to make sure that
    // $httpRequest->isSameSite() returns true in Form.php
    wire()->input->cookie->set(Helpers::StrictCookieName, '1');

    require_once __DIR__ . "/RockFormsPage.php";
    $this->wire->classLoader->addNamespace("RockForms", __DIR__ . "/classes");
    $this->wire->classLoader->addNamespace("RockForms", __DIR__ . "/PageClasses");
    $this->wire->classLoader->addNamespace("RockForms\Renderer", __DIR__ . "/RockFormsRenderer");
    $this->wire->classLoader->addNamespace(
      "RockForms\Renderer",
      $this->wire->config->paths->templates . "RockForms/Renderer"
    );
    $this->wire->classLoader->addNamespace("RockForms\Controls", __DIR__ . "/controls");
    $this->wire->classLoader->addNamespace(
      "RockForms",
      $this->wire->config->paths->templates . "RockForms"
    );
    $this->wire('rockforms', $this);
    $this->forms = new WireData();
    $this->renderedMarkup = new WireData();

    /** @var RockMigrations $rm */
    $rm = $this->wire->modules->get('RockMigrations');
    $rm->watch($this);

    // create minified assets
    if (
      wire()->config->rockdevtools
      && wire()->modules->isInstalled('RockDevTools')
    ) {
      rockdevtools()->assets()->minify(__DIR__ . '/src', __DIR__ . '/dst');
    }

    // sanitize success parameter
    $this->successParam = $this->wire->sanitizer->pageName($this->successParam);
    if (!$this->successParam) $this->successParam = 'form-success';

    // hooks
    wire()->addHookAfter("Page::render", $this, "hookDoubleSubmit");
    wire()->addHookAfter("Page::render", $this, "hookAddJsWarning");
    wire()->addHook("/" . $this->confirmParam . "/{key}/", $this, "handleConfirm");
    wire()->addHook("/rockforms-csrf/", $this, "hookCreateCSRF");
    wire()->addHookAfter('InputfieldText::render', $this, 'hookFormSelectField');

    // hide rootpage from tree
    if (!$this->showDataPage) {
      $this->addHookAfter("ProcessPageList::find", $this, "hideRootPage");
      $this->addHookBefore('ProcessPageListRender::getNumChildren', $this, "hookNumChildren");
    }

    // add rockloader if the module is installed
    // we don't want rockloaders to be a requirement for this module
    if (wire()->modules->isInstalled('RockLoaders')) {
      rockloaders()->add('dots');
    }

    $this->addAutoDeleteHooks();
  }

  // ### regular methods ###

  private function addAutoDeleteHooks(): void
  {
    wire()->addHookAfter('Modules::refresh', $this, 'autoDeleteEntries');
    wire()->addHookAfter('Session::login', $this, 'autoDeleteEntries');
    wire()->addHookAfter('Pages::added', $this, 'autoDeleteEntries');
  }

  protected function autoDeleteEntries(): void
  {
    $days = $this->keepDays;
    if (!$days) return;
    $timestamp = time() - RockMigrations::oneDay * $days;
    $entries = wire()->pages->find([
      'include' => 'all',
      'template' => Entry::tpl,
      'created<' => $timestamp,
    ]);
    foreach ($entries as $entry) $entry->delete();
  }

  /**
   * Get forms that are listed in the <select> field to select a form
   */
  public function ___getSelectableForms(HookEvent $event): WireData
  {
    return $this->getForms();
  }

  public function checkbox($val, $tooltip = false)
  {
    if ($val) {
      $t = $tooltip ? 'title=yes uk-tooltip' : '';
      return '<svg ' . $t . ' xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="m9 12l2 2l4-4"/><path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9-9 9s-9-1.8-9-9s1.8-9 9-9z"/></g></svg>';
    } else {
      $t = $tooltip ? 'title=no uk-tooltip' : '';
      return '<svg ' . $t . ' xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c7.2 0 9 1.8 9 9s-1.8 9-9 9s-9-1.8-9-9s1.8-9 9-9z"/></svg>';
    }
  }

  private function configBackend(&$inputfields)
  {
    $fs = new InputfieldFieldset();
    $fs->label = "Backend";
    $fs->icon = "sitemap";
    $inputfields->add($fs);

    $fs->add([
      'type' => 'checkbox',
      'name' => 'showDataPage',
      'label' => 'Show Datapage in Pagetree for Superusers',
      'checked' => $this->showDataPage ? 'checked' : '',
      'columnWidth' => 100,
    ]);
  }

  private function configDebug(&$inputfields)
  {
    $fs = new InputfieldFieldset();
    $fs->label = "Debug";
    $fs->icon = "bug";
    $inputfields->add($fs);

    $fs->add([
      'type' => 'checkbox',
      'name' => 'logFormSubmissions',
      'label' => 'Log Form Submissions to log "form-submissions"',
      'notes' => 'All form values will be logged as JSON string. This happens before processInput() and before any spam protection is applied, so it is useful for debugging only.',
      'checked' => $this->logFormSubmissions ? 'checked' : '',
      'columnWidth' => 100,
    ]);
  }

  private function configFrontend(&$inputfields)
  {
    $fs = new InputfieldFieldset();
    $fs->label = "Frontend (Form Submission & Validation)";
    $fs->icon = "paper-plane-o";
    $inputfields->add($fs);

    $fs->add([
      'type' => 'checkbox',
      'name' => 'noHTMX',
      'label' => 'HTMX',
      'description' => 'By default RockForms will submit forms via HTMX to provide great usability out of the box.',
      'checkboxLabel' => 'Disable HTMX Form Submission',
      'notes' => '[See docs for details](https://www.baumrock.com/en/processwire/modules/rockforms/docs/).',
      'checked' => $this->noHTMX ? 'checked' : '',
      'columnWidth' => 50,
    ]);
    $fs->add([
      'type' => 'checkbox',
      'name' => 'noHtmxModal',
      'label' => 'HTMX Modal',
      'description' => 'By default RockForms add markup and styles for a modal that pops up when submitting the form.',
      'checkboxLabel' => 'Disable HTMX Modal + CSS Markup',
      'notes' => 'The markup will be injected at the bottom of your <body>
        [See docs for details](https://www.baumrock.com/en/processwire/modules/rockforms/docs/).',
      'checked' => $this->noHtmxModal ? 'checked' : '',
      'columnWidth' => 50,
    ]);

    $fs->add([
      'type' => 'checkbox',
      'name' => 'noLiveValidation',
      'label' => 'Live-Validation',
      'description' => 'By default RockForms will add live-validation to your forms - that means users will get instant validation feedback without submitting the form to the server (and waiting for the response). Thx to NetteForms that JavaScript logic is automatically applied from your PHP code - you don\'t have to code it twice!',
      'checkboxLabel' => "Disable Live-Validation",
      'notes' => "[See docs for details and a live demo](https://www.baumrock.com/en/processwire/modules/rockforms/docs/validation/)",
      'checked' => $this->noLiveValidation ? 'checked' : '',
      'columnWidth' => 100,
    ]);

    $fs->add([
      'type' => 'text',
      'name' => 'confirmParam',
      'label' => 'Double-Opt-In',
      'description' => 'Here you can customise the endpoint for double-opt-in processes if you need to support such workflows to comply with GDPR or simply want to confirm the correctnes of mail addresses etc.',
      'notes' => 'Default: yourdomain.com/forms-confirm/123abc
        [See docs for details](https://www.baumrock.com/en/processwire/modules/rockforms/docs/opt-in/)',
      'value' => $this->confirmParam,
      'columnWidth' => 50,
    ]);

    $fs->add([
      'type' => 'text',
      'name' => 'successParam',
      'label' => 'Success Parameter',
      'description' => 'Specify a URL parameter to redirect to upon successful form submission.',
      'notes' => 'Default: yourdomain.com/your/page/?form-success=DemoForm
        See docs about [double form submissions](https://www.baumrock.com/en/processwire/modules/rockforms/docs/double/) for details and a demo.
        Also see docs about [tracking form submissions when using XTMX](https://www.baumrock.com/en/processwire/modules/rockforms/docs/htmx/#tracking-form-submissions).',
      'value' => $this->successParam,
      'columnWidth' => 50,
    ]);

    $fs->add([
      'type' => 'markup',
      'label' => 'RockForms.js Documentation',
      'value' => 'Please see the <a href="https://www.baumrock.com/en/processwire/modules/rockforms/docs/js/" target="_blank">documentation about RockForms.js</a> for more information on how to properly integrate and use it in your projects.',
    ]);
  }

  private function configGDPR(&$inputfields)
  {
    $fs = new InputfieldFieldset();
    $fs->label = "GDPR";
    $fs->icon = "gavel";
    $inputfields->add($fs);

    $fs->add([
      'type' => 'integer',
      'name' => 'keepDays',
      'label' => 'Auto-Delete Entries after ... Days',
      'icon' => 'trash',
      'value' => $this->keepDays,
      'notes' => 'Set to "30" to delete entries after 30 days. Empty means no automatic deletion.',
      'columnWidth' => 100,
    ]);
  }

  private function configSpam(&$inputfields)
  {
    $fs = new InputfieldFieldset();
    $fs->label = "Spam Protection";
    $fs->icon = "filter";
    $fs->description = 'RockForms spam protection is designed to be as simple as possible for the user filling out the form. Another important requirement is to make it work with ProCache\'d pages which is why we use JavaScript-based techniques as much as possible. Is that 100% accurate or secure? No. Does it work for my clients? Yes :) [See docs for details](https://www.baumrock.com/en/processwire/modules/rockforms/docs/spam/).';
    $inputfields->add($fs);

    $fs->add([
      'type' => 'textarea',
      'name' => 'honeypotfields',
      'description' => 'To use honeypot fields for your forms enter one fieldname per line. Existing fieldnames will be skipped, so make sure to use good looking names like "message", "comment", "email" or such that you dont use yourself.',
      'label' => 'Honeypot Fields',
      'value' => $this->honeypotfields,
      'notes' => "Enter one per line. If you don't want to use honeypots at all leave this field empty.
        Note: add .rf-hny { display: none; } to your site's CSS to hide the honeypot fields.",
      'rows' => 3,
      'columnWidth' => 50,
    ]);

    $fs->add([
      'type' => 'integer',
      'name' => 'submitdelay',
      'label' => 'Submit Delay',
      'description' => 'Set a delay in seconds to prevent spam by analyzing the submission speed. Spam-Bots usually fill forms very quickly - humans don\'t!',
      'value' => $this->submitdelay,
      'notes' => 'I recommend a setting of 2, which means that all forms submitted within 2 seconds after page load will be considered spam.
      A setting of 0 disables this feature.',
      'columnWidth' => 50,
    ]);

    $fs->add([
      'type' => 'checkbox',
      'name' => 'dontBlockSpammers',
      'label' => 'WireRequestBlocker',
      'description' => 'If WireRequestBlocker is installed RockForms will block spammers if they submit a form and the submission is considered spam.',
      'checkboxLabel' => 'Do not block spammers using WireRequestBlocker',
    ]);
  }

  public function entriesPage(): Entries|NullPage
  {
    return $this->wire->pages->get([
      'template' => Entries::tpl,
      'parent' => $this->rootPage(),
    ]);
  }

  public function getCSRF(): string
  {
    $rand = new WireRandom();
    $name = $rand->alphanumeric();
    $token = $rand->alphanumeric();
    $this->wire->session->set(self::csrfstring . $name, $token);
    return $name . self::csrfstring . $token;
  }

  public function getEntry($key): Entry|false
  {
    $entry = $this->wire->pages->get([
      'template' => Entry::tpl,
      'parent' => $this->entriesPage(),
      'name' => $key,
    ]);
    return $entry->id ? $entry : false;
  }

  public function getForm($form, $silent = false, $context = null): RockForm|false
  {
    $name = (string)$form;
    if ($f = $this->forms->get($name)) return $f;

    $file = $this->getForms()->get($name);
    if ($silent && !$file) return false;
    return $this->getFormFromFile($file, $context);
  }

  public function getFormFromFile($file, $context = null)
  {
    if (!is_file((string)$file)) throw new WireException("File $file not found");
    $name = pathinfo($file)['filename'];
    if ($f = $this->forms->get($name)) return $f;
    require_once $file;

    $rf = $this->rockfrontend();
    if ($rf) $rf->setTextdomain($file);

    // set context
    if (!$context) $context = [];
    if (is_array($context)) $context = (new WireData())->setArray($context);
    if (!$context instanceof WireData) {
      throw new WireException("Context must be either array or WireData");
    }

    $class = "\\RockForms\\$name";
    $formName = $context->formName ?: $name;
    $form = new $class($formName);
    if (!$form instanceof RockForm) throw new WireException("Invalid Form");

    $form->context = $context;

    // add CSRF token
    $form->addCSRF();

    // we add honeypots at the very top
    // this hopefully helps to trick spammers that try to submit the form
    // after filling the fields one by one
    $form->addHoney();

    // add htmx markup if it is not disabled
    if ($form::HTMX !== false && !$this->noHTMX) $form->useHTMX();

    // add regular form fields
    $form->buildForm();

    // add submit delay
    if ($form::SUBMITDELAY !== false) $form->addSubmitDelay();

    $this->forms->set($formName, $form);

    if ($rf) $rf->setTextdomain();

    return $form;
  }

  /**
   * Get a WireData object holding all form names and file paths
   * @return WireData
   */
  public function getForms(): WireData
  {
    if ($this->formsWireDataObject) return $this->formsWireDataObject;
    $forms = new WireData();

    // look for forms in all RockForms folders
    // this is a hardcoded array but might be extendable in the future
    // at the moment it looks in /site/templates/RockForms
    // and in /site/modules/*/RockForms/
    $files = array_merge(
      glob($this->wire->config->paths->templates . "RockForms/*.php"),
      glob($this->wire->config->paths->siteModules . "*/RockForms/*.php"),
    );

    // populate wiredata object
    foreach ($files as $file) {
      $name = pathinfo($file, PATHINFO_FILENAME);
      $forms->set($name, $file);
    }

    return $this->formsWireDataObject = $forms;
  }

  /**
   * Get array ready to be used in RockPageBuilder settings field
   * @return array
   * @throws WireException
   */
  public function getFormSelectArray(): array
  {
    $forms = $this->getForms()->getArray();
    return array_combine(array_keys($forms), array_keys($forms));
  }

  /**
   * Config inputfields
   * @param InputfieldWrapper $inputfields
   */
  public function getModuleConfigInputfields($inputfields)
  {
    $name = strtolower($this);
    $inputfields->add([
      'type' => 'markup',
      'label' => 'Documentation & Updates',
      'icon' => 'life-ring',
      'value' => "<p>Hey there, coding rockstars! 👋</p>
        <ul>
          <li><a class=uk-text-bold href=https://www.baumrock.com/modules/$name/docs>Read the docs</a> and level up your coding game! 🚀💻😎</li>
          <li><a class=uk-text-bold href=https://www.baumrock.com/rock-monthly>Sign up now for our monthly newsletter</a> and receive the latest updates and exclusive offers right to your inbox! 🚀💻📫</li>
          <li><a class=uk-text-bold href=https://github.com/baumrock/$name>Show some love by starring the project</a> and keep me motivated to build more awesome stuff for you! 🌟💻😊</li>
          <li><a class=uk-text-bold href=https://paypal.me/baumrockcom>Support my work with a donation</a>, and together, we'll keep rocking the coding world! 💖💻💰</li>
        </ul>",
    ]);

    $this->configGDPR($inputfields);
    $this->configSpam($inputfields);
    $this->configFrontend($inputfields);
    $this->configBackend($inputfields);
    $this->configDebug($inputfields);
    return $inputfields;
  }

  public function handleConfirm(HookEvent $event)
  {
    $entry = $this->getEntry($event->key);
    if (!$entry) throw new Wire404Exception("Invalid key");
    if ($this->wire->input->get->confirm) {
      $entry->confirm(true);
      $form = $entry->getForm();

      // if the form implements an onConfirm method we trigger it now
      if ($form instanceof RockForm) {
        if (method_exists($form, "onConfirm")) $form->onConfirm($entry);
      }

      // if the form does not redirect somewhere we redirect to the homepage
      $this->wire->session->redirect("/");
    }
    return $this->wire->files->render(__DIR__ . "/lib/confirm.php", [
      'event' => $event,
    ]);
  }

  /**
   * Hide rootpage from page tree
   */
  public function hideRootPage(HookEvent $event)
  {
    $event->return = $event->return->remove(
      $this->wire->pages->get("/rockforms")
    );
  }

  /**
   * Get names of honeyfields to add to each form
   * @return array
   */
  public function honeyFields(): array
  {
    $arr = explode("\n", $this->honeypotfields);
    $arr = array_map(function ($item) {
      return $this->wire->sanitizer->fieldName($item);
    }, $arr);
    return array_filter($arr);
  }

  /**
   * We show a warning for superusers if a form uses CSRF but JS is not loaded
   * @param HookEvent $event
   * @return void
   * @throws WireException
   * @throws WirePermissionException
   */
  protected function hookAddJsWarning(HookEvent $event): void
  {
    if (!$this->wire->user->isSuperuser()) return;
    if (!$this->wire->config->debug) return;
    $html = $event->return;
    $event->return = str_replace(
      "</body>",
      '<script>
      document.addEventListener("DOMContentLoaded", () => {
        setTimeout(() => {
          let csrfInput = document.querySelector(".RockForm input[name=csrf]");
          if (csrfInput && typeof RockForms === "undefined") {
            alert("CSRF needs RockForms.js to work!");
          }
        }, 50);
      });
      </script></body>',
      $html
    );
  }

  /**
   * Url hook for returning a CSRF token
   * @param HookEvent $event
   * @return void|string
   * @throws WireException
   */
  protected function hookCreateCSRF(HookEvent $event)
  {
    if (!$this->wire->config->ajax) return;
    return $this->getCSRF();
  }

  public function hookDoubleSubmit(HookEvent $event)
  {
    $successForm = $this->wire->input->get($this->successParam, 'string');
    if (!$successForm) return;
    foreach ($this->rendered() as $form) {
      if ($form->name !== $successForm) continue;
      $url = $this->wire->input->queryString([$this->successParam => null]);
      if ($url) $url = "?$url";
      $this->wire->session->redirect(
        $this->wire->input->url() . $url,
        false // 302
      );
    }
  }

  public function hookField(
    callable $callback,
    ?Control $control = null
  ): void {
    wire()->addHookAfter(
      'RockForms::renderField',
      function (HookEvent $event) use ($control, $callback) {
        $c = $event->arguments(1);
        if ($control && $c->name !== $control->name) return;
        $dom = rockfrontend()->dom($event->return);
        $field = $dom->children()->first();
        $callback($field);
        $event->return = $dom->html();
      }
    );
  }

  /**
   * Turn a regular text field into a <select> field to select a form
   */
  protected function hookFormSelectField(HookEvent $event): void
  {
    $f = $event->object;
    if (!str_starts_with($f->name, 'rockforms_form')) return;
    $forms = $this->getSelectableForms($event);
    $options = '<option></option>';
    foreach ($forms as $name => $form) {
      $selected = $name === $f->value ? ' selected' : '';
      $options .= "<option value='$name'$selected>$name</option>";
    }
    $markup = "<select name='{$f->name}'>$options</select>";
    $event->return = $markup;
  }

  /**
   * Hook num children when rootpage was removed
   */
  public function hookNumChildren(HookEvent $event)
  {
    $page = $event->arguments(0);
    if ($page->id === 1) $page->numChildren = $page->numChildren - 1;
  }

  public function html($str)
  {
    $rockfrontend = $this->wire->modules->get('RockFrontend');
    if (!$rockfrontend) return $str;
    return $rockfrontend->html($str);
  }

  public function migrate()
  {
    /** @var RockMigrations $rm */
    $rm = $this->wire->modules->get('RockMigrations');
    $rm->migratePageClasses(__DIR__ . "/PageClasses", "RockForms", "RockForms");
    $rm->setParentChild(Entries::tpl, Entry::tpl);
    $root = $rm->createPage(
      template: Root::tpl,
      parent: 1,
      name: 'rockforms',
      title: 'RockForms',
      status: ['hidden']
    );
    $rm->createPage(
      template: Entries::tpl,
      parent: $root,
      name: 'entries',
      title: 'Entries'
    );
  }

  /**
   * Render this form
   *
   * $context is to provide context for your form, eg you might want to provide
   * different mailto addresses for the same form depending on where the form
   * is rendered. The context will be available in your form as $form->context
   */
  public function render(string $name, $context = null)
  {
    if (!$name) return false;
    if ($markup = $this->renderedMarkup->get($name)) return $markup;
    if (is_file($name)) {
      $form = $this->getFormFromFile($name, $context);
    } else {
      $form = $this->getForm($name, context: $context);
    }
    if ($form instanceof RockForm) {
      $markup = $form->renderReturn();
      $this->renderedMarkup->set($form->name, $markup);
      return $this->html($markup);
    }
    return false;
  }

  public function rendered(?RockForm $form = null)
  {
    $rendered = $this->rendered ?: $this->wire(new WireArray());
    if (!$form) return $rendered;
    $rendered->add($form);
    $this->rendered = $rendered;
  }

  /**
   * Load renderer by name
   *
   * Usage:
   * $form->setRenderer($rockforms->renderer('UIkitRenderer'));
   */
  public function renderer($name): FormRenderer
  {
    if ($name instanceof FormRenderer) return $name;
    $class = "\RockForms\Renderer\\$name";
    $renderer = new $class();
    return $renderer;
  }

  /**
   * Hookable method to intercept rendering of all fields (controls)
   */
  public function ___renderField(RockFormsRenderer $renderer, Control $control)
  {
    return $renderer->renderPairHelper($control);
  }

  /**
   * Hookable method to intercept rendering of all fields (controls)
   */
  public function ___renderFieldMulti(RockFormsRenderer $renderer, array $controls)
  {
    return $renderer->renderPairMultiHelper($controls);
  }

  /**
   * Hookable method to intercept rendering of all fields (controls)
   */
  public function ___renderFields(RockFormsRenderer $renderer, $parent)
  {
    return $renderer->renderControlsHelper($parent);
  }

  public function rockfrontend(): RockFrontend|null
  {
    return $this->wire->modules->get('RockFrontend');
  }

  /**
   * Return RockForms Root Page
   */
  public function rootPage(): Root|NullPage
  {
    return $this->wire->pages->get([
      'template' => Root::tpl,
      'parent' => 1,
    ]);
  }

  public function scriptTag(): string
  {
    $file = $this->wire->config->urls($this) . "dst/RockForms.min.js";
    $url = $this->wire->config->versionUrl($file);
    return "<script src='$url' defer></script>";
  }

  /**
   * Set global error messages for the form validator
   * This is to set global translations eg in /site/ready.php
   *
   * Usage to use predefined translations:
   * $rockforms->setErrors('de');
   *
   * Usage with custom error messages:
   * $rockforms->setErrors(
   *   Form::FILLED => 'Bitte füllen Sie dieses Feld aus',
   * );
   *
   * You can also combine both:
   * $rockforms->setErrors('de');
   * $rockforms->setErrors(
   *   Form::FILLED => 'My custom required error',
   * );
   */
  public function setErrors($messages): void
  {
    if (is_string($messages)) {
      // load messages from translation file
      $messages = strtolower($messages);
      $messages = $this->wire->files->render(__DIR__ . "/errors/$messages.php");
    }
    if (!is_array($messages)) throw new WireException("Error translations must be an array.");
    Validator::$messages = array_merge(Validator::$messages, $messages);
  }
}
