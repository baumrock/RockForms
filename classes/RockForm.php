<?php

namespace RockForms;

use Nette\Forms\Controls\TextInput;
use Nette\Forms\Form;
use Nette\InvalidStateException;
use Nette\InvalidArgumentException;
use ProcessWire\ProcessWire;
use ProcessWire\RockForms;
use ProcessWire\RockFrontend;
use ProcessWire\RockMails;
use ProcessWire\WireData;
use ProcessWire\WireException;
use ProcessWire\WirePermissionException;
use ReflectionClass;
use ReflectionException;
use RockForms\Controls\Markup;

use function ProcessWire\rockmigrations;
use function ProcessWire\wire;

class RockForm extends Form
{
  public $fieldTags = [];
  public $prependMarkup = false;
  public $appendMarkup = false;

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
    // attach render hook that tells rockforms that this form
    // has been rendered (necessery for redirects)
    $this->onRender[] = function (RockForm $form) {
      $form->rockforms()->rendered($form);
    };
    $this->onValidate[] = [$this, 'processInput'];
    $this->init();
  }

  public function init()
  {
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
        ->addRule($this::BLANK);
      /** @var TextInput $control */
      $control->setOption("rockforms-honey", true);
    }
  }

  /**
   * Add custom markup control
   * @return Markup
   */
  public function addMarkup($str, $name = null)
  {
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
    $this->addComponent($control, $name ?: uniqid());
  }

  public function addSubmitDelay(): void
  {
    $delay = $this->rockforms()->submitdelay;
    if (!$delay) return;
    $id = "timeonpage-" . uniqid();
    $this->addText("timeonpage")
      ->setHtmlAttribute("id", $id)
      ->setHtmlAttribute("hidden", true)
      ->addRule(
        $this::MIN,
        "Please wait a moment before submitting the form and try again",
        $delay
      );
    $file = realpath(__DIR__ . "/../includes/wait.php");
    $script = $this->wire->files->render($file, ['id' => $id]);
    $this->addMarkup("<div hidden>{timeonpage}$script</div>");
  }

  /**
   * Append markup after the <form> element
   */
  public function appendMarkup(string $markup): void
  {
    $this->appendMarkup .= $markup;
  }

  /**
   * Should be implemented by Forms
   */
  public function buildForm()
  {
  }

  public function className($short = true)
  {
    if ($short) return (new ReflectionClass($this))->getShortName();
    return get_class($this);
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
   * Get current url
   * By default this will also include the query string
   * eg /foo/?bar=baz
   */
  public function getUrl($params = true)
  {
    if (!$params) return $this->wire->input->url();
    $query = $this->wire->input->queryString();
    return $this->wire->input->url() . ($query ? "?$query" : "");
  }

  /**
   * Get submitted values without honeypot fields
   */
  public function getValuesWithoutHoney(): WireData
  {
    $values = new WireData();
    $values->setArray($this->getValues('array'));
    foreach ($this->getFields() as $name => $field) {
      if (!$field->getOption("rockforms-honey")) continue;
      $values->remove($name);
    }
    return $values;
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
   * Prepend markup before the <form> element
   */
  public function prependMarkup(string $markup): void
  {
    $this->prependMarkup = $markup;
  }

  /**
   * Should be implemented by Forms
   */
  public function processInput()
  {
  }

  /**
   * Renders form.
   */
  public function render(...$args): void
  {
    if ($this->showSuccess()) {
      if (method_exists($this, "renderSuccess")) {
        // get submitted values from session and reset session afterwards
        // this to persist values across the submit-redirect-pattern
        $values = $this->wire()->wire(new WireData());
        $arr = $this->wire()->session->rockformValues;
        if (is_array($arr)) $values->setArray($arr);
        $this->wire()->session->rockformValues = false;

        // render success message
        echo $this->renderSuccess($values);
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

  public function renderTable($values = null): string
  {
    if (!$values) $values = $this->getValues();
    return rockmigrations()->renderTable($values);
  }

  public function rockforms(): RockForms
  {
    return $this->wire()->modules->get('RockForms');
  }

  public function rockfrontend(): RockFrontend
  {
    return $this->wire->modules->get('RockFrontend');
  }

  public function rockmails(): RockMails
  {
    return $this->wire->modules->get('RockMails');
  }

  public function saveEntry($title, $values = null): Entry
  {
    if (!$values) $values = $this->getValuesWithoutHoney()->getArray();

    // save entry
    $entry = new Entry();
    $entry->set('title', $title);
    $entry->set(Entry::field_form, $this->className());
    $entry->set(Entry::field_labels, json_encode($this->fieldLabels()));
    $entry->set(Entry::field_values, json_encode((array)$values));
    $entry->save();

    // save meta data to this entry
    $entry->meta('url', $this->getUrl());

    return $entry;
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

      $query = $this->wire->input->queryString();
      $url = $this->wire->input->url() . "?$query";
      if ($query) $url .= "&";
      $session->redirect("{$url}$param={$this->name}");
    }
  }

  public function submitCount(): int
  {
    return (int)$this->rockforms()->submitCount->get($this->name);
  }

  public function successParam()
  {
    return $this->wire()->input->get(
      $this->rockforms()->successParam,
      'string'
    );
  }

  public function wire(): ProcessWire
  {
    return wire();
  }
}
