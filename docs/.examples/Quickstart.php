<?php

namespace RockForms;

class Quickstart extends RockForm
{
  const CSRF = false;

  public function buildForm()
  {
    $form = $this;
    $form->useHTMX();
    $form->setRockFormsRenderer('UIkit');
    $form->addText("forename", "Enter your first name")
      ->setRequired("We need your first name to show it!");
    $f = $form->addText("surname", "Enter your given name")
      ->getLabelPrototype();
    // bd($f);
    $form->addSubmit("submit", "Submit your name");
  }

  public function processInput()
  {
    $form = $this;
    $values = $form->getValues();
    if ($values->surname == 'baz') {
      $form['surname']->addError('Sorry, baz is not allowed as given name.');
    }
  }

  public function renderSuccess($values)
  {
    $name = $values->forename;
    if ($values->surname) $name .= " " . $values->surname;
    return "<div class='uk-alert uk-alert-success'>
      <p>Thank you for submitting the form, $name!</p>
      <p>Now try to reload the page and see what happens: The form will not be submitted again (which is a typical pitfall when building forms on your own). Instead it will reload the page and present you a fresh new form ready for you to fill out ;)</p>
      </div>";
  }
}
