<?php

namespace RockForms;

use ProcessWire\RockForms;

trait RockFormsPage
{
  public function rockforms(): RockForms
  {
    return $this->wire->modules->get('RockForms');
  }
}
