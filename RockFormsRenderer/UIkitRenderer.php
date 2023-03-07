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
    $this->wrappers['controls']['container'] = null;
    $this->wrappers['pair']['container'] = 'div';
    $this->wrappers['error']['container'] = 'div class="uk-alert-danger" uk-alert';
    $this->wrappers['error']['item'] = 'div';

    $this->wrappers['control']['errorcontainer'] = 'div class="uk-alert-danger uk-margin-remove" uk-alert';
    $this->wrappers['control']['erroritem'] = '';

    $this->wrappers['group']['container'] = 'div class=uk-margin-small';
    $this->wrappers['group']['label'] = 'div';

    $this->wrappers['label']['container'] = 'div class=uk-form-label';
    $this->wrappers['control']['container'] = 'div class="uk-form-controls uk-margin-small"';
  }

  /**
   * Provides complete form rendering.
   * @param  string  $mode  'begin', 'errors', 'ownerrors', 'body', 'end' or empty to render all
   */
  public function render(Form $form, ?string $mode = null): string
  {
    foreach ($form->getControls() as $control) {
      if ($control instanceof RadioList) {
        $control->getControlPrototype()->addClass('uk-radio');
        $control->getSeparatorPrototype()->setName('span class="uk-margin-small-left"');
        $control->getContainerPrototype()->addClass('uk-alert-danger');
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
