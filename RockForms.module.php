<?php

namespace ProcessWire;

use RockForms\RockForm;

/**
 * @author Bernhard Baumrock, 07.03.2023
 * @license Licensed under MIT
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
    $this->wire('rockforms', $this);

    // sanitize success parameter
    $this->successParam = $this->wire->sanitizer->pageName($this->successParam);
    if (!$this->successParam) $this->successParam = 'form-success';

    // hooks
    $this->wire->addHookAfter("Page::render", $this, "hookDoubleSubmit");
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

  public function rendered(RockForm $form = null)
  {
    $rendered = $this->rendered ?: $this->wire(new WireArray());
    if (!$form) return $rendered;
    $rendered->add($form);
    $this->rendered = $rendered;
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
    return $inputfields;
  }
}
