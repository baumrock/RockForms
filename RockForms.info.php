<?php

namespace ProcessWire;

$info = [
  'title' => 'RockForms',
  'version' => json_decode(file_get_contents(__DIR__ . "/package.json"))->version,
  'summary' => 'Simple, secure and versatile forms based on NetteForms.',
  'autoload' => true,
  'singular' => true,
  'icon' => 'paper-plane-o',
  'requires' => [
    'PHP>=8.0',
  ],
  'installs' => [],
];
