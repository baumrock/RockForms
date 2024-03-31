<?php

namespace ProcessWire;

/**
 * @author Bernhard Baumrock, 26.03.2023
 * @license COMMERCIAL PLEASE DO NOT DISTRIBUTE
 * @link https://www.baumrock.com
 */
class TextformatterRockForms extends Textformatter
{
  public static function getModuleInfo()
  {
    return [
      'title' => 'RockForms',
      'version' => '1.0.0',
      'summary' => 'Demo Textformatter',
      'requires' => 'RockForms',
    ];
  }

  public function format(&$str)
  {
    if (strpos($str, "[rockforms=") === false) return;
    preg_replace_callback("/\[rockforms=(.*?)\]/", function ($match) use (&$str) {
      $tag = $match[0];
      $name = $match[1];
      $form = $this->forms()->render($name);
      if ($form) $str = str_replace($tag, $form, $str);
    }, $str);
  }

  public function forms(): RockForms
  {
    return $this->wire->modules->get('RockForms');
  }
}
