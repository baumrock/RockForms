<?php

namespace RockForms;

use ProcessWire\Wire;

class MultiStepForm extends Wire
{
  public $activeStep;

  public $prefix = '';

  /** @var StepsArray */
  public $steps;

  public function __construct()
  {
    $this->steps = new StepsArray();
  }

  public function addSteps(array $steps): self
  {
    $i = 0;
    foreach ($steps as $name => $data) {
      if (is_int($name) && is_string($data)) {
        // we have a name-only step
        $name = $data;
        $data = [];
      }
      $step = new Step($name, $data);
      if ($i++ === 0) $step->first = true;
      $this->steps->add($step);
      $step->init($this);
    }
    $this->steps->get("name=$name")->last = true;
    $this->activeStep->active = true;
    return $this;
  }

  public function getFirstUndone(): Step|false
  {
    foreach ($this->steps as $step) {
      if (!$step->done) return $step;
    }
    return false;
  }

  public function getLastDone(): Step|false
  {
    $last = false;
    foreach ($this->steps as $step) {
      if (!$step->done) return $last;
      $last = $step;
    }
    return $last;
  }

  public function getLastViewable(): Step|false
  {
    $last = false;
    foreach ($this->steps as $step) {
      if (!$step->viewable()) return $last;
      $last = $step;
    }
    return false;
  }

  /**
   * Get the current step to render
   */
  public function getStep(): Step|false
  {
    if (!$this->steps->count()) return false;
    if ($step = $this->wire->input->get('step', 'string')) {
      $step = $this->steps->get("name=$step");
      if (!$step instanceof Step || !$step->viewable()) {
        $this->wire->session->redirect($this->getLastViewable()->url);
      }
      return $step;
    }
    return $this->steps->first();
  }

  public function __debugInfo(): array
  {
    return [
      'steps' => $this->steps,
      'activeStep' => $this->activeStep,
    ];
  }
}
