<?php

namespace RockForms;

class Grid extends RockForm
{
  public function buildForm()
  {
    $this->setRockFormsRenderer("UIkit");
    $this->addText('forename', 'Forename');
    $this->addText('surname', 'Surname');
    $this->addMarkup("<div class='uk-child-width-1-2 uk-grid-collapse' uk-grid>
      <div>{forename}</div>
      <div><div class='uk-margin-small-left'>{surname}</div></div>
      </div>");
    $this->addText('full', 'Full Width');
  }
}
