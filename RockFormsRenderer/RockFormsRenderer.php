<?php

namespace RockForms\Renderer;

use Nette\Forms\Rendering\DefaultFormRenderer;
use ProcessWire\RockForms;
use RockForms\RockForm;

use function ProcessWire\wire;

class RockFormsRenderer extends DefaultFormRenderer
{

  public function getForm(): RockForm
  {
    return $this->form;
  }

  /**
   * Renders single visual row.
   * Overrides the default renderControls method to make it hookable
   * See RockForms::renderControls for usage
   */
  public function renderControls($parent): string
  {
    return $this->rockforms()->renderControls($this, $parent);
  }

  /**
   * Helper method for $rockforms->renderControls() hookable method
   */
  public function renderControlsParent($parent)
  {
    return parent::renderControls($parent);
  }

  public function rockforms(): RockForms
  {
    return wire()->modules->get('RockForms');
  }
}
