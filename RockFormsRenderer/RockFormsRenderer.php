<?php

namespace RockForms\Renderer;

use Nette\Forms\Control;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Forms\Rendering\DefaultFormRenderer;
use ProcessWire\RockForms;
use RockForms\Controls\Markup;
use RockForms\RockForm;

use function ProcessWire\wire;

class RockFormsRenderer extends DefaultFormRenderer
{
  public function __construct()
  {
    $this->wrappers['controls']['container'] = null;

    // add special tags to the container for individual css styling
    // this is a RockForms feature, so it will not work in Nette FormRenderers
    $this->wrappers['pair']['container'] = 'div class="field-{fieldname} type-{fieldtype}"';
    $this->wrappers['error']['container'] = 'div class="form-errors"';
    $this->wrappers['error']['item'] = 'div class="form-error"';

    $this->wrappers['control']['errorcontainer'] = 'div class="field-error"';
    $this->wrappers['control']['erroritem'] = '';

    $this->wrappers['group']['container'] = 'div';
    $this->wrappers['group']['label'] = 'div';

    $this->wrappers['label']['container'] = 'div class="form-label"';
    $this->wrappers['control']['container'] = 'div class="form-control"';
  }

  public function getForm(): RockForm
  {
    return $this->form;
  }

  /**
   * Return a Nette Html object
   * Usage: $renderer->html("<strong>foo bar</strong>");
   */
  public function html(string $html)
  {
    return \Nette\Utils\Html::el()->setHtml($html);
  }

  /**
   * Provides complete form rendering.
   * @param  string  $mode  'begin', 'errors', 'ownerrors', 'body', 'end' or empty to render all
   */
  public function render(Form $form, ?string $mode = null): string
  {
    $form->getElementPrototype()->addClass('RockForm');
    return
      $form->prependMarkup .
      parent::render($form, $mode) .
      $form->appendMarkup;
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
      $html = $this->replaceTags(parent::renderPair($control), $control);
      $form->fieldTags[$control->name] = $html;
      return '';
    }

    // regular nette forms control --> regular rendering
    return $this->replaceTags(parent::renderPair($control), $control);
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
    return $this->replaceTags(
      parent::renderPairMulti($controls),
      $controls
    );
  }

  public function replaceTags($markup, $control)
  {
    if (is_array($control)) $control = $control[0];
    /** @var BaseControl $control */
    $markup = str_replace("{fieldname}", $control->name, $markup);
    $markup = str_replace("{fieldtype}", $control->getOption('type'), $markup);
    return $markup;
  }

  public function rockforms(): RockForms
  {
    return wire()->modules->get('RockForms');
  }
}
