<?php

namespace RockForms;

use ProcessWire\WireData;

use function ProcessWire\rockforms;

class Step extends WireData
{
  const sessionName = 'rockforms-step-';

  public $active = false;

  /** @var MultiStepForm */
  public $multiStepForm;

  public $done = false;

  public $form = false;

  public $headline;
  public $name;
  public $num;
  public $url;

  public function __construct(string $name, array $data = [])
  {
    $this->name = $name;
    $this->url = "./?step=$name";
    foreach ($data as $k => $v) $this->$k = $v;

    // load data from session storage
    foreach ($this->getData() as $k => $v) {
      $this->$k = $v;
    }
  }

  public function init(MultiStepForm $multiStepForm): void
  {
    $stepname = $this->wire->input->get('step', 'string');
    $this->multiStepForm = $multiStepForm;
    $this->num = $multiStepForm->steps->count();

    // populate form
    if ($this->form === false) {
      $this->form = $this->multiStepForm->prefix . ucfirst($this->name);
    }

    // set active step
    // if not yet set, we set it to the current item (first item)
    if (!$multiStepForm->activeStep || ($stepname && $stepname === $this->name)) {
      $multiStepForm->activeStep = $this;
    }
  }

  // ##### regular methods #####

  public function clickable(): bool
  {
    return $this->viewable() && !$this->active;
  }

  /**
   * Get step-data from session storage
   */
  public function getData(): WireData
  {
    $data = new WireData();
    $raw = $this->wire->session->get(self::sessionName . $this->name);
    if (!is_array($raw)) $raw = [];
    $data->setArray($raw);
    return $data;
  }

  public function getForm(): RockForm|false
  {
    return rockforms()->getForm($this->form, true);
  }

  public function next(): Step|null
  {
    return $this->multiStepForm->steps->getNext($this);
  }

  public function nextViewable(): Step|false
  {
    $step = false;
    foreach ($this->multiStepForm->steps as $s) {
      if (!$s instanceof Step) continue;
      if (!$s->viewable()) return $step;
      $step = $s;
    }
    return $step;
  }

  public function prev(): Step|null
  {
    return $this->multiStepForm->steps->getPrev($this);
  }

  /**
   * Set properties and save data to session
   *
   * Usage:
   * $step->save([
   *   'done' => true,
   *   'firstname' => 'John',
   *   'lastname' => 'Doe',
   * ]);
   */
  public function save(array $data): self
  {
    $_data = $this->getData();
    foreach ($data as $k => $v) $_data->$k = $v;
    $this->wire->session->set(
      self::sessionName . $this->name,
      $_data->getArray()
    );
    return $this;
  }

  /**
   * Redirect to next step
   * @return void
   */
  public function toNext(): void
  {
    $next = $this->next();
    if ($next) $this->wire->session->redirect($next->url);
    $this->wire->session->redirect('./');
  }

  public function viewable(): bool
  {
    return true;
  }

  public function __debugInfo()
  {
    return [
      'num' => $this->num,
      'name' => $this->name,
      'form' => $this->form,
      'url' => $this->url,
      'headline' => $this->headline,
      'done' => $this->done,
    ];
  }
}
