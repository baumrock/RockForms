<?php

namespace ProcessWire;

use Nette\Forms\Control;
use Nette\Forms\FormRenderer;
use RockForms\Entries;
use RockForms\Entry;
use RockForms\Renderer\RockFormsRenderer;
use RockForms\RockForm;
use RockForms\Root;

/**
 * @author Bernhard Baumrock, 07.03.2023
 * @license COMMERCIAL PLEASE DO NOT DISTRIBUTE
 * @link https://www.baumrock.com
 */
class RockForms extends WireData implements Module, ConfigurableModule
{
  public $confirmParam = "forms-confirm";

  /** @var WireArray */
  public $rendered;

  /** @var WireData */
  public $renderedMarkup;

  public $submitCount;
  public $successParam = false;

  private $forms;

  public function init()
  {
    require_once __DIR__ . "/RockFormsPage.php";
    require_once __DIR__ . "/vendor/autoload.php";
    $this->wire->classLoader->addNamespace("RockForms", __DIR__ . "/classes");
    $this->wire->classLoader->addNamespace("RockForms", __DIR__ . "/PageClasses");
    $this->wire->classLoader->addNamespace("RockForms\Renderer", __DIR__ . "/RockFormsRenderer");
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

    // sanitize success parameter
    $this->successParam = $this->wire->sanitizer->pageName($this->successParam);
    if (!$this->successParam) $this->successParam = 'form-success';

    // hooks
    $this->wire->addHookAfter("Page::render", $this, "hookDoubleSubmit");
    $this->wire->addHookAfter("Page::render", $this, "hookAddAssets");
    $this->wire->addHook("/" . $this->confirmParam . "/{key}/", $this, "handleConfirm");
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

  public function entriesPage(): Entries|NullPage
  {
    return $this->wire->pages->get([
      'template' => Entries::tpl,
      'parent' => $this->rootPage(),
    ]);
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

  /**
   * @return RockForm
   */
  public function getForm($form)
  {
    $name = (string)$form;
    if ($f = $this->forms->get($name)) return $f;
    $dir = $this->wire->config->paths->templates . "RockForms";
    return $this->getFormFromFile("$dir/$name.php");
  }

  public function getFormFromFile($file)
  {
    if (!is_file($file)) throw new WireException("File $file not found");
    $name = pathinfo($file)['filename'];
    if ($f = $this->forms->get($name)) return $f;
    require_once $file;

    $rf = $this->rockfrontend();
    if ($rf) $rf->setTextdomain($file);

    $class = "\\RockForms\\$name";
    $form = new $class($name);
    $form->buildForm();
    $this->forms->set($name, $form);

    if ($rf) $rf->setTextdomain();

    return $form;
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

  public function hookAddAssets(HookEvent $event)
  {
    if (!$this->rendered()->count()) return;
    $assets = '';
    if ($this->wire->config->debug) {
      $assets .= "<!-- RockForms Assets added in RockForms.module.php -->";
    }

    if (!$this->noLiveValidation) {
      if ($opt = $this->LiveFormOptions) {
        $assets .= "<script>LiveFormOptions = " . json_encode($opt) . "</script>";
      }
      $live = $this->wire->config->urls->root . "site/modules/RockForms/lib/live-form-validation.min.js";
      $assets .= "<script src=$live defer></script>";
    }

    $event->return = str_replace("</head>", "$assets</head>", $event->return);
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
        $this->wire->input->url() . $url
      );
    }
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

  public function render(string $name)
  {
    if ($markup = $this->renderedMarkup->get($name)) return $markup;
    if (is_file($name)) {
      $form = $this->getFormFromFile($name);
    } else $form = $this->getForm($name);
    if ($form instanceof RockForm) {
      $markup = $form->renderReturn();
      $this->renderedMarkup->set($form->name, $markup);
      return $this->html($markup);
    }
    return false;
  }

  public function rendered(RockForm $form = null)
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

  /**
   * Render form values as uikit table
   */
  public function renderTable($values, $labels = [], $tooltips = false)
  {
    if (is_string($values)) $values = json_decode($values);
    if (is_array($labels)) $labels = (new WireData())->setArray($labels);
    $out = "<table class='uk-table uk-table-small uk-table-striped uk-margin-remove'>";
    foreach ($values as $k => $v) {
      if (is_bool($v)) $v = $this->checkbox($v, $tooltips);
      $label = $labels->get($k) ?: $k;
      $t = $tooltips ? "title='$k' uk-tooltip" : "";
      $out .= "<tr>
          <td class='uk-width-expand'>
            <span class='uk-text-small uk-text-muted' $t>$label</span><br>
            $v
          </td>
        </tr>";
    }
    $out .= "</table>";
    return $out;
  }

  public function rockfrontend(): RockFrontend
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

  /**
   * Config inputfields
   * @param InputfieldWrapper $inputfields
   */
  public function getModuleConfigInputfields($inputfields)
  {
    $inputfields->add([
      'type' => 'text',
      'name' => 'confirmParam',
      'label' => 'URL Confirmation Parameter',
      'value' => $this->confirmParam,
    ]);
    $inputfields->add([
      'type' => 'text',
      'name' => 'successParam',
      'label' => 'URL Success Parameter',
      'notes' => 'Forms that have no custom action redirect to this url parameter on successful form submission.'
        . "\nCurrent: ?{$this->successParam}=...",
      'value' => $this->successParam,
    ]);
    $url = "https://github.com/contributte/live-form-validation";
    $inputfields->add([
      'type' => 'checkbox',
      'name' => 'noLiveValidation',
      'description' => 'By default RockForms will load the live-validation script automatically whenever a form is rendered on the page.',
      'label' => 'Live-Validation',
      'checkboxLabel' => "Don't inject the live-validation script automatically",
      'notes' => "See [$url]($url)",
      'checked' => $this->noLiveValidation ? 'checked' : '',
    ]);
    return $inputfields;
  }
}
