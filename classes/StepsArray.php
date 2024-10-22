<?php

namespace RockForms;

use ProcessWire\WireArray;
use ProcessWire\WireData;

class StepsArray extends WireArray
{
  public function getData(
    $stepname = null,
    $returnArrays = false,
  ): array|WireData {
    $data = [];
    foreach ($this as $step) {
      $data[$step->name] = $returnArrays
        ? $step->getData()->getArray()
        : $step->getData();
    }
    if ($stepname) return $data[$stepname];
    if ($returnArrays) return $data;
    return (new WireData())->setArray($data);
  }
}
