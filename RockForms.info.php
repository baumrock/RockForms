<?php

namespace ProcessWire;

/**
 * @author Bernhard Baumrock, 07.03.2023
 * @license COMMERCIAL PLEASE DO NOT DISTRIBUTE
 * @link https://www.baumrock.com
 */
$info = [
  'title' => 'RockForms',
  'version' => json_decode(file_get_contents(__DIR__ . "/package.json"))->version,
  'summary' => 'Simple, secure and versatile forms based on NetteForms.',
  'autoload' => true,
  'singular' => true,
  'icon' => 'paper-plane-o',

  // PHP8 for named arguments
  // rockmigrations 4.3.0 for renderTable() method
  'requires' => [
    'PHP>=8.0',
    'RockMigrations>=4.3.0',
  ],

  'installs' => [
    'ProcessRockForms',
  ],
];
