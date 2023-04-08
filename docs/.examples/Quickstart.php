<?php

namespace RockForms;

class Quickstart extends RockForm
{

  public function buildForm()
  {
    $form = $this;
    $form->setRockFormsRenderer('UIkit');
    $form->addText("forename", "Enter your first name")
      ->setRequired("We need your first name to show it!");
    $f = $form->addText("surname", "Enter your given name")
      ->getLabelPrototype();
    bd($f);
    $form->addSubmit("submit", "Submit your name");
  }

  public function processInput()
  {
    $values = $this->getValues();
    if ($values->surname == 'baz') {
      $this['surname']->addError('--- something went wrong on the server ---');
    }
  }

  public function renderSuccess($values)
  {
    $name = $values->forename;
    if ($values->surname) $name .= " " . $values->surname;
    return "<div class='uk-alert uk-alert-success'>Thank you for submitting the form, $name!</div>";
  }
}
