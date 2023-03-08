<?php

namespace RockForms\Renderer;

use Nette\Forms\Controls\RadioList;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Form;

/**
 * See https://gitlab.com/baumrock/RockForms/-/blob/master/renderers/UIKitRenderer.php
 */
class UIkitRenderer extends RockFormsRenderer
{
  public function __construct()
  {
    parent::__construct();

    // set options for live form validation
    // if live-form-validation script is used this
    // will make sure that errors on live validation look
    // the same as non-live errors.
    $this->rockforms()->LiveFormOptions = [
      "messageErrorClass" => "uk-alert uk-alert-danger uk-margin-remove uk-display-block",
    ];

    // see RockFormsRenderer for all other wrapper settings
    $this->wrappers['error']['item'] = 'div class="uk-alert-danger" uk-alert';
    $this->wrappers['control']['errorcontainer'] = 'div class="uk-alert-danger uk-margin-remove" uk-alert';
    $this->wrappers['label']['container'] = 'div class="uk-form-label"';
    $this->wrappers['control']['container'] = 'div class="uk-form-controls"';
  }

  /**
   * Provides complete form rendering.
   * @param  string  $mode  'begin', 'errors', 'ownerrors', 'body', 'end' or empty to render all
   */
  public function render(Form $form, ?string $mode = null): string
  {
    $form->setHtmlAttribute('uk-grid');
    $form->getElementPrototype()->addClass('uk-child-width-1-1');

    foreach ($form->getControls() as $control) {
      if ($control instanceof RadioList) {
        $control->getControlPrototype()->addClass('uk-radio uk-margin-small-right');
      } elseif ($control instanceof TextInput) {
        $control->getControlPrototype()
          ->addClass('uk-input');
      } elseif ($control instanceof TextArea) {
        $control->getControlPrototype()
          ->addClass('uk-textarea');
      } elseif ($control instanceof SubmitButton) {
        $control->getControlPrototype()->addClass('uk-button uk-button-primary');
      }
    }

    return parent::render($form, $mode);
  }
}
