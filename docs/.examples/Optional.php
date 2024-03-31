<?php

namespace RockForms;

use Nette\Forms\Controls\BaseControl;
use ProcessWire\HookEvent;
use RockForms\Renderer\UIkit;

class Optional extends RockForm
{
  const CSRF = false;

  public function init()
  {
    $this->wire->addHookBefore("RockForms::renderField", function (HookEvent $event) {
      /** @var BaseControl $control */
      $control = $event->arguments(1);
      if ($control->isRequired()) {
        $control->getLabelPrototype()->addClass('uk-text-bold');
      } else {
        /** @var UIkit $renderer */
        $renderer = $event->arguments(0);
        $label = $control->label->getText() . " <small>(optional)</small>";
        $control->setCaption($renderer->html($label));
      }
    });
  }

  public function buildForm()
  {
    $this->setRockFormsRenderer("UIkit");
    $this->addText('forename', 'Forename')
      ->setRequired();
    $this->addText('surname', 'Surname');
  }
}
