<?php

namespace RockForms\Renderer;

use Nette\Forms\Control;
use Nette\Forms\Form;
use Nette\Forms\Rendering\DefaultFormRenderer;
use ProcessWire\RockForms;
use RockForms\Controls\Markup;
use RockForms\RockForm;

use function ProcessWire\wire;

class RockFormsRenderer extends DefaultFormRenderer
{

  public function getForm(): RockForm
  {
    return $this->form;
  }

  /**
   * Provides complete form rendering.
   * @param  string  $mode  'begin', 'errors', 'ownerrors', 'body', 'end' or empty to render all
   */
  public function render(Form $form, ?string $mode = null): string
  {
    $form->getElementPrototype()->addClass('RockForm');
    return parent::render($form, $mode);
  }

  /**
   * Renders single visual row.
   * Overrides the default renderControls method to make it hookable
   * See RockForms::renderControls for usage
   */
  public function renderControls($parent): string
  {
    return $this->rockforms()->renderFields($this, $parent);
  }

  /**
   * Helper for hook in RockForms
   */
  public function renderControlsHelper($parent)
  {
    return parent::renderControls($parent);
  }

  /**
   * Renders single visual row.
   */
  public function renderPair(Control $control): string
  {
    return $this->rockforms()->renderField($this, $control);
  }

  /**
   * Helper for hook in RockForms
   */
  public function renderPairHelper(Control $control): string
  {
    // if it is a markup field we render it directly
    if ($control instanceof Markup) return $control->render();

    // if it is a field that is part of the fieldTags array we dont render it
    // now, but we save the markup for injecting in later into the markup
    // of a markup field
    $form = $this->getForm();
    $hidden = array_keys($form->fieldTags);
    if (in_array($control->name, $hidden)) {
      $html = parent::renderPair($control);
      $form->fieldTags[$control->name] = $html;
      return '';
    }

    // regular nette forms control --> regular rendering
    return parent::renderPair($control);
  }

  /**
   * Renders single visual row of multiple controls.
   * @param  Nette\Forms\Control[]  $controls
   */
  public function renderPairMulti(array $controls): string
  {
    return $this->rockforms()->renderFieldMulti($this, $controls);
  }

  /**
   * Helper for hook in RockForms
   */
  public function renderPairMultiHelper(array $controls): string
  {
    return parent::renderPairMulti($controls);
  }

  public function rockforms(): RockForms
  {
    return wire()->modules->get('RockForms');
  }
}
