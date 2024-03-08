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

  // rockmigrations 4.1.0 for minify directory feature
  'requires' => [
    'PHP>=8.0',
    'RockMigrations>=4.1.0',
  ],

  'installs' => [
    'ProcessRockForms',
  ],
];
