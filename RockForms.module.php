<?php

namespace ProcessWire;

use Nette\Forms\Control;
use Nette\Forms\FormRenderer;
use RockForms\Renderer\RockFormsRenderer;
use RockForms\RockForm;

/**
 * @author Bernhard Baumrock, 07.03.2023
 * @license COMMERCIAL PLEASE DO NOT DISTRIBUTE
 * @link https://www.baumrock.com
 */
class RockForms extends WireData implements Module, ConfigurableModule
{
  /** @var WireArray */
  public $rendered;

  public $submitCount;
  public $successParam = false;

  public function init()
  {
    require_once __DIR__ . "/vendor/autoload.php";
    $this->wire->classLoader->addNamespace("RockForms", __DIR__ . "/classes");
    $this->wire->classLoader->addNamespace("RockForms\Renderer", __DIR__ . "/RockFormsRenderer");
    $this->wire->classLoader->addNamespace("RockForms\Controls", __DIR__ . "/controls");
    $this->wire('rockforms', $this);

    // sanitize success parameter
    $this->successParam = $this->wire->sanitizer->pageName($this->successParam);
    if (!$this->successParam) $this->successParam = 'form-success';

    // hooks
    $this->wire->addHookAfter("Page::render", $this, "hookDoubleSubmit");
    $this->wire->addHookAfter("Page::render", $this, "hookAddAssets");
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
      $this->wire->session->redirect("./");
    }
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
   * Config inputfields
   * @param InputfieldWrapper $inputfields
   */
  public function getModuleConfigInputfields($inputfields)
  {
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
