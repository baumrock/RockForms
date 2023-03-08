<?php

namespace RockForms\Controls;

/**
 * Nette forms control to render custom markup
 *
 * The internal usage would be this:
 * $form->add('Markup', 'foo')->set('This is some <strong>markup</strong>');
 *
 * Every RockForm provides a shortcut though:
 * $form->addMarkup("foo <strong>bar</strong>");
 *
 * The shortcut also makes it possible to render other fields from within your
 * html string:
 * $form->addMarkup("
 *   <table><tr><td>{field1}</td><td>{field2}</td></tr></table>
 * ");
 *
 * @author Bernhard Baumrock, 21.01.2021
 * @link https://www.baumrock.com
 */

use Nette\Forms\Controls\BaseControl;

class Markup extends BaseControl
{

  private $html;

  public function __construct()
  {
    // prevent it from being in $form->getValues()
    $this->setDisabled(true);
    $this->setOption('type', 'markup');
  }

  /**
   * Return the final markup
   * This method is RockForms specific and not part of nette Controls
   * @return string
   */
  public function render()
  {
    $html = $this->html;
    foreach ($this->getForm()->fieldTags as $name => $markup) {
      $html = str_replace("{{$name}}", $markup, $html);
    }
    return $html;
  }

  /**
   * Set final ouput html of this Control
   * @return void
   */
  public function setHtml($html)
  {
    $this->html = $html;
    return $this;
  }
}
