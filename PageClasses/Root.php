<?php

namespace RockForms;

use ProcessWire\Page;
use RockMigrations\MagicPage;

class Root extends Page
{
  use MagicPage;

  const tpl = "rockforms_root";

  public function migrate()
  {
    $rm = $this->rockmigrations();
    $rm->migrate([
      'fields' => [],
      'templates' => [
        self::tpl => [
          'noChildren' => true,
        ],
      ],
    ]);
  }
}
