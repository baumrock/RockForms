<?php

namespace RockForms;

use Nette\Forms\Control;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Form;
use Nette\Http\FileUpload;
use Nette\InvalidStateException;
use Nette\InvalidArgumentException;
use Nette\NotSupportedException;
use Nette\Utils\RegexpException;
use ProcessWire\ProcessWire;
use ProcessWire\RockForms;
use ProcessWire\RockFrontend;
use ProcessWire\RockMails;
use ProcessWire\WireData;
use ProcessWire\WireTempDir;
use ReflectionClass;
use RockForms\Controls\Markup;

use function ProcessWire\rockforms;
use function ProcessWire\rockmigrations;
use function ProcessWire\wire;

class RockForm extends Form
{
  const CSRF = true;
  const HTMX = true;
  const noRedirectPattern = false;
  const SUBMITDELAY = true;
  const honeyErrorMessage = "Sorry, we don't like Spam!";

  public $appendMarkup = false;
  public $context;
  public $debug = false;
  public $fieldTags = [];
  public $isSpam = false;
  public $noHTMX = false;
  public $prependMarkup = false;

  /** @var ProcessWire */
  public $wire;

  /**
   * Custom constructor
   * RockForms must have a name!
   */
  public function __construct(string $name)
  {
    parent::__construct($name);
    $this->wire = $this->wire();
    $this->setRockFormsRenderer('RockFormsRenderer');
    $this->setHtmlAttribute('data-rooturl', wire()->config->urls->root);
    if (rockforms()->debug) $this->debug();

    // attach render hook that tells rockforms that this form
    // has been rendered (necessery for redirects)
    $this->onRender[] = function (RockForm $form) {
      $form->rockforms()->rendered($form);
    };
    $this->onValidate[] = function (RockForm $form) {
      if ($form::CSRF) $form->validateCSRF();
      // first we check the form for spam
      $form->validateAntiSpam();

      // log form submissions
      if (rockforms()->logFormSubmissions) {
        $values = $form->getValues();
        foreach ($values as $k => $v) {
          switch ($k) {
            case 'password':
            case 'password_confirm':
            case 'pass':
              $values[$k] = '***';
              break;
          }
        }
        wire()->log->save(
          'form-submissions',
          wire()->session->getIP() . " ::: " . json_encode($values)
        );
      }

      // then we trigger processInput so the user can add logic
      // and maybe set isSpam manually (when using external tools)
      try {
        $form->processInput();
      } catch (\Throwable $th) {
        $form->addError($th->getMessage());
        return;
      }

      // log spam and block spammers
      $form->saveToLog();
      $form->blockSpammer();

      // trigger success method
      $form->triggerProcessSuccess();
    };
    $this->init();
  }

  public function init() {}

  /**
   * Add CSRF protection to this form
   * @return void
   * @throws InvalidStateException
   * @throws InvalidArgumentException
   */
  public function addCSRF(): void
  {
    // no csrf --> early exit
    if (!$this::CSRF) return;
    if ($this::CSRF === 'pageload') {
      // we load the csrf token instantly on pageload
      // this is for forms without any caching (eg RockCommerce checkout)
      $this->addText("csrf", "CSRF Token")
        ->setOption('rockforms-system', true)
        ->setHtmlAttribute('no-reset', true)
        ->setValue(rockforms()->getCSRF());
    } elseif ($this::CSRF === 'domready') {
      // load csrf on domready
      // this is for forms on cached pages that should be ready instantly
      // like a login form that might be auto-filled by the browser
      $this->addText("csrf", "CSRF Token")
        ->setOption('rockforms-system', true)
        ->getControlPrototype()
        ->addClass('domready');
    } else {
      // CSRF === true
      // this is for forms on procached pages
      // CSRF will be loaded via ajax on form submit
      $this->addText("csrf", "CSRF Token")
        ->setOption('rockforms-system', true);
    }
    $this->addMarkup("<div hidden>{csrf}</div>");
  }

  /**
   * Add honeypot fields to form
   */
  public function addHoney(): void
  {
    $fields = $this->getFields();
    $honeyFields = $this->rockforms()->honeyFields();
    foreach ($honeyFields as $field) {
      if ($fields->$field) continue;
      $control = $this->addText($field, "Please enter $field")
        ->setHtmlAttribute("autocomplete", "off")
        ->addRule($this::BLANK, self::honeyErrorMessage);
      /** @var TextInput $control */
      $control->setOption("rockforms-honey", true);
    }
  }

  /**
   * Add custom markup control
   * @return Markup
   */
  public function addMarkup(
    $str,
    $name = null,
    ?string $insertBefore = null,
  ) {
    // try to find tags that represent other fields
    foreach ($this->getControls() as $control) {
      if (strpos($str, "{{$control->name}}") === false) continue;
      // we found a tag like {fieldname}
      // we add the fieldname to the array of fields that will
      // later be hidden and injected at the new position
      $this->fieldTags[$control->name] = true;
    }

    $control = new Markup();
    $control->setHtml($str);
    $this->addComponent($control, $name ?: uniqid(), $insertBefore);
  }

  public function addSubmitDelay(): void
  {
    $delay = $this->rockforms()->submitdelay;
    if (!$delay) return;
    $debug = $this->debug;
    $msg = "Please wait a moment before submitting the form and try again";
    $control = $this->addText("timeonpage")
      ->setOption('rockforms-system', true)
      ->setHtmlAttribute("hidden", $debug ? false : true)
      ->addRule($this::Filled, $msg)
      ->addRule($this::Min, $msg, $delay);
    $control->setOption("rockforms-submitdelay", true);
    $file = realpath(__DIR__ . "/../includes/wait.php");
    $script = wire()->files->render($file);
    $style = $debug ?: 'position:absolute;top:0;left:0;width:0;height:0;overflow:hidden;';
    $this->addMarkup("
      <div style='$style'>
        <div class='field'>{timeonpage}</div>$script
      </div>
    ");
  }

  /**
   * Append markup after the <form> element
   */
  public function appendMarkup(string $markup): void
  {
    $this->appendMarkup .= $markup;
  }

  private function blockSpammer(): void
  {
    if ($this->hasErrors()) return;
    if (!$this->isSpam) return;
    if (rockforms()->dontBlockSpammers) return;
    if (!wire()->modules->isInstalled('WireRequestBlocker')) return;
    wire()->modules->get('WireRequestBlocker')
      ->blocker()
      ->blockIp($_SERVER['REMOTE_ADDR'], [
        'url' => wire()->page->httpUrl(),
        'reason' => "Form Spam",
      ]);
  }

  /**
   * Should be implemented by Forms
   */
  public function buildForm() {}

  public function className($short = true)
  {
    if ($short) return (new ReflectionClass($this))->getShortName();
    return get_class($this);
  }

  public function debug(): void
  {
    $this->debug = true;
    $this->addMarkup("
      <style>
        #{$this->getID()} .rf-hny {
          display: block !important;
        }
        #{$this->getID()} *[hidden] {
          display: block !important;
        }
      </style>
    ");
  }

  public function fieldLabels(): array
  {
    $arr = [];
    $texttools = $this->wire()->sanitizer->getTextTools();
    foreach ($this->getComponents(true) as $c) {
      $arr[$c->getName()] = $texttools->markupToText((string)$c->caption);
    }
    return $arr;
  }

  public function firstComponent()
  {
    foreach ($this->getComponents() as $c) return $c;
  }

  /**
   * Alias for getComponent (more PW like)
   * @return \Nette\Forms\Controls\BaseControl
   */
  public function getField($name)
  {
    return $this->getComponent($name);
  }

  /**
   * Get all fields of the form as WireData object (for nicer syntax)
   */
  public function getFields(): WireData
  {
    $names = [];
    foreach ($this->getControls() as $c) $names[$c->name] = $c;
    return (new WireData)->setArray($names);
  }

  /**
   * Shortcut to get form element's id
   * @return string
   * @throws NotSupportedException
   * @throws RegexpException
   */
  public function getID(): string
  {
    return $this->getElementPrototype()->getAttribute("id");
    return $this->getElementPrototype()->id;
  }

  /**
   * Get submitted values without system fields like honeypot/csrf
   */
  public function getNonSystemValues(): WireData
  {
    $values = new WireData();
    $values->setArray($this->getValues('array'));
    foreach ($this->getFields() as $name => $field) {
      $remove = false;
      if ($field->getOption("rockforms-honey")) $remove = true;
      elseif ($field->getOption("rockforms-system")) $remove = true;
      if ($remove) $values->remove($name);
    }
    return $values;
  }

  public function getSanitizedFilename($file): string
  {
    return wire()->sanitizer->fileName($file->getUntrustedName());
  }

  /**
   * Get current url
   * By default this will also include the query string
   * eg /foo/?bar=baz
   */
  public function getUrl($params = true)
  {
    if (!$params) return wire()->input->url();
    $query = wire()->input->queryString();
    return wire()->input->url() . ($query ? "?$query" : "");
  }

  /**
   * Return a Nette Html object
   * Usage:
   * $form->addText(
   *   "your_fieldname",
   *   $form->html("<strong>foo bar</strong>"
   * );
   */
  public function html(string $html)
  {
    return \Nette\Utils\Html::el()->setHtml($html);
  }

  /**
   * Login user by mail and password
   */
  protected function login(
    string $mail,
    string $password,
    string $errorMessage,
  ): bool {
    $user = wire()->users->get("email=$mail");
    if (!$user->id) {
      $this->addError($errorMessage);
      return false;
    }
    try {
      $loggedInUser = wire()->session->login($user, $password);
      if (!$loggedInUser) {
        $this->addError($errorMessage);
        return false;
      }
      return true;
    } catch (\Throwable $th) {
      $this->addError($th->getMessage());
      return false;
    }
  }

  /**
   * Prepend markup before the <form> element
   */
  public function prependMarkup(string $markup): void
  {
    $this->prependMarkup = $markup;
  }

  /**
   * Should be implemented by Forms
   */
  public function processInput() {}

  /**
   * Should be implemented by Forms
   */
  public function processSuccess() {}

  /**
   * Renders form.
   */
  public function render(...$args): void
  {
    /**
     * If noRedirectPattern is true, we use the original Nette render method
     * This is useful for HTMX forms, where we want to show a success message
     * on top of the form and render the form again (without redirects)
     */
    if ($this::noRedirectPattern) {
      // this is necessary to trigger the onValidate event of the form
      // which then calls processInput and processSuccess
      if ($this->isSuccess()) {
        $this->addMarkup(
          $this->successMarkup(),
          insertBefore: $this->firstComponent()->name
        );
      }
      parent::render(...$args);
      return;
    }

    if ($this->showSuccess()) {
      if (method_exists($this, "renderSuccess")) {
        // get submitted values from session and reset session afterwards
        // this is to persist values across the submit-redirect-pattern
        $values = $this->wire()->wire(new WireData());
        $arr = $this->wire()->session->rockformValues;
        if (is_array($arr)) $values->setArray($arr);
        $this->wire()->session->rockformValues = false;

        // entity encode all valuse to be safe for direct ouput
        foreach ($values as $k => $v) {
          $val = $v;
          if ($v instanceof FileUpload) {
            $val = $v->hasFile() ? $v->getSanitizedName() : '';
          }
          $values[$k] = wire()->sanitizer->entities1($val);
        }

        // render success message
        // we wrap it in a div with the id of the form to make sure
        // when using HTMX we know what content to swap!
        echo "<div id='{$this->getID()}'>"
          . $this->renderSuccess($values)
          . "</div>";
        return;
      }
    }
    echo parent::render(...$args);
  }

  /**
   * Nette's render method renders the form using direct "echo"
   * This method is a wrapper that calls render and catches the output via
   * output buffer and then returns the generated markup for later use.
   */
  public function renderReturn(...$args): string
  {
    ob_start();
    echo $this->render(...$args);
    return ob_get_clean();
  }

  public function renderTable($values = null, $options = []): string
  {
    if (!$values) $values = $this->getValues();
    return rockmigrations()->renderTable($values, $options);
  }

  public function rockforms(): RockForms
  {
    return $this->wire()->modules->get('RockForms');
  }

  public function rockfrontend(): RockFrontend
  {
    return wire()->modules->get('RockFrontend');
  }

  public function rockmails(): RockMails
  {
    return wire()->modules->get('RockMails');
  }

  public function saveEntry($title, $values = null, $saveFiles = true): Entry
  {
    if (!$values) $values = $this->getNonSystemValues()->getArray();

    // save entry
    $entry = new Entry();
    $entry->set('title', $title);
    $entry->set(Entry::field_form, $this->className());
    $entry->set(Entry::field_labels, json_encode($this->fieldLabels()));
    $entry->set(Entry::field_values, json_encode($this->sleepValues($values)));
    $entry->save();

    // save submitted files
    if ($saveFiles) {
      $files = [];
      foreach ($values as $k => $file) {
        if ($file instanceof FileUpload) $files[] = $file;
        else if (is_array($file)) $files = array_merge($files, $file);
      }
      foreach ($files as $file) {
        if (!$file instanceof FileUpload) continue;
        if (!$file->hasFile()) continue;

        $tmpfilename = $file->getTemporaryFile();
        $newbasename = $this->getSanitizedFilename($file);
        $tmp = new WireTempDir("rockforms-form-" . $this->getID());
        $newfilename = $tmp . $newbasename;
        move_uploaded_file($tmpfilename, $newfilename);

        $filesfield = $entry->getUnformatted($entry::field_files);
        $filesfield->add($newfilename);
        $entry->set($entry::field_files, $filesfield);
      }
      $entry->save($entry::field_files);
    }

    // save meta data to this entry
    $entry->meta('url', $this->getUrl());
    $entry->meta('ip', wire()->session->getIP());

    return $entry;
  }

  private function saveToLog(): void
  {
    if (!$this->isSpam) return;
    if ($this->hasErrors()) return;
    $values = $this->values(true);
    $values->_ip = wire()->session->getIP();
    wire()->log->save(
      'rockforms-spam',
      print_r($values->getArray(), true),
      ['url' => '']
    );
  }

  /**
   * Set RockForms Renderer by name
   * Usage: $form->setRockFormsRenderer('UIkitRenderer');
   */
  public function setRockFormsRenderer(string $name)
  {
    $this->setRenderer($this->rockforms()->renderer($name));
  }

  public function showSuccess()
  {
    if ($this->getAction()) return;
    $session = $this->wire()->session;

    // if session flag is set we show the success message
    if ($session->rockformSuccess === $this->name) {
      $session->rockformSuccess = false;
      return true;
    }

    // form was successfully submitted, so we set the session flag
    // and redirect to the current page with success get parameter
    if ($this->isSuccess()) {
      $session->rockformSuccess = $this->name;
      $session->rockformValues = (array)$this->getValues();
      $param = $this->rockforms()->successParam;

      $query = wire()->input->queryString();
      $url = wire()->input->url() . "?$query";
      if ($query) $url .= "&";
      $url = "{$url}$param={$this->name}";
      $session->redirect(
        $this->successRedirectUrl($url),
        false // 302
      );
    }
  }

  public function sleepValue($val): mixed
  {
    if (is_string($val)) return $val;
    if ($val instanceof FileUpload) {
      if ($val->hasFile()) return $this->getSanitizedFilename($val);
      return '';
    }
    if (is_array($val)) {
      $str = "";
      foreach ($val as $item) {
        // file upload?
        if ($item instanceof FileUpload) {
          if (!$item->hasFile()) continue;
          $str .= $this->getSanitizedFilename($item) . "\n";
        } else {
          $str .= $this->sleepValue($item);
        }
      }
      return $str;
    }
    return $val;
  }

  public function sleepValues($values): array
  {
    $arr = [];
    foreach ($values as $k => $v) {
      try {
        $arr[$k] = (string)$this->sleepValue($v);
      } catch (\Throwable $th) {
        $arr[$k] = $th->getMessage();
      }
    }
    return $arr;
  }

  public function submitCount(): int
  {
    return (int)$this->rockforms()->submitCount->get($this->name);
  }

  public function successMarkup()
  {
    return '--- Add method successMarkup() to your form to change this message ---';
  }

  public function successParam()
  {
    return $this->wire()->input->get(
      $this->rockforms()->successParam,
      'string'
    );
  }

  /**
   * Method that returns the redirect url after success
   *
   * Overwrite this method in case you need to modify the redirect url
   *
   * @param string $url
   * @return string
   */
  public function successRedirectUrl(string $url): string
  {
    return $url;
  }

  private function triggerProcessSuccess(): void
  {
    if ($this->isSpam) return;
    if ($this->hasErrors()) return;

    if (wire()->config->debug) {
      $this->processSuccess();
      return;
    }

    // try to execute processSuccess and catch exceptions
    // exceptions will be shown as error message to the user
    try {
      $this->processSuccess();
    } catch (\Throwable $th) {
      wire()->log->save('rockforms', $th->getMessage());
      $this->addError("There was an error processing your request. Error has been logged. Please try again.");
    }
  }

  /**
   * This will add the default HTMX html attributes to support htmx submissions
   * @return void
   * @throws NotSupportedException
   * @throws RegexpException
   */
  public function useHTMX(): void
  {
    // get current query string
    // this is to make sure we send the request to the same page
    // for example we might be on a page /foo/?bar=123 and we want to
    // submit the form to /foo/?bar=123 and not just /foo/
    $query = wire()->input->queryString();
    $this->setHtmlAttribute("hx-post", "./?nocache&{$query}");
    $this->setHtmlAttribute("hx-swap", "outerHTML");
    if (wire()->config->rockformsPreserveSuccess) {
      $this->setHtmlAttribute("hx-swap", "afterend");
    }
    $this->setHtmlAttribute("hx-select", "#" . $this->getID());
  }

  /**
   * Validate spam protection fields
   * @return void
   */
  private function validateAntiSpam(): void
  {
    foreach ($this->getControls() as $control) {
      if (!(
        $control->getOption("rockforms-honey")
        || $control->getOption("rockforms-submitdelay")
      )) continue;
      if (count($control->getErrors())) $this->isSpam = true;
    }
  }

  /**
   * Validate CSRF protection
   * @return void
   */
  private function validateCSRF(): void
  {
    foreach ($this->getControls() as $control) {
      if ($control->name !== 'csrf') continue;
      if (!$control instanceof TextInput) continue;
      $parts = explode(RockForms::csrfstring, $control->getValue(), 2);
      if (count($parts) !== 2) continue;
      $key = RockForms::csrfstring . $parts[0];
      $token = $parts[1];
      $_token = wire()->session->get($key);
      if ($token === $_token) {
        wire()->session->remove($key);
        return; // early exit, no error
      }
    }
    $this->addError("Invalid CSRF token");
  }

  /**
   * Get values of form
   * @param bool $systemFields
   * @return WireData
   */
  public function values($systemFields = false): WireData
  {
    $values = new WireData();
    $values->setArray($this->getValues('array'));
    if ($systemFields === false) {
      foreach ($this->getFields() as $name => $field) {
        $remove = false;
        if ($field->getOption("rockforms-honey")) $remove = true;
        elseif ($field->getOption("rockforms-system")) $remove = true;
        if ($remove) $values->remove($name);
      }
    }
    return $values;
  }

  public function wire(): ProcessWire
  {
    return wire();
  }
}
