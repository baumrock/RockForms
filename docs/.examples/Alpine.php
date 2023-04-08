<?php

namespace RockForms;

class Alpine extends RockForm
{

  public function buildForm()
  {
    $this->rockfrontend()->scripts()->add(
      "https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js",
      "defer"
    );
    $this->setHtmlAttribute("x-data", "{name: 'Bernhard'}");
    $this->setRockFormsRenderer("UIkit");
    $this->addMarkup("<div class='uk-margin'>
      You habe a lovely name, <strong x-text='name'></strong>!
      </div>");
    $this->addText("name", "Enter your name")
      ->setHtmlAttribute('x-model', 'name');
  }
}
