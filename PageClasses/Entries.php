<?php

namespace RockForms;

use ProcessWire\Inputfield;
use ProcessWire\Page;
use RockMigrations\MagicPage;

class Entries extends Page
{
  use MagicPage;

  const tpl = "rockforms_entries";
  const prefix = "rockforms_entries_";

  public function addable()
  {
    return false;
  }

  public function editable()
  {
    return false;
  }

  public function migrate()
  {
    $rm = $this->rockmigrations();
    $rm->migrate([
      'fields' => [],
      'templates' => [
        self::tpl => [
          'fields' => [
            'title' => [
              'collapsed' => Inputfield::collapsedNoLocked,
            ],
          ],
          'sortfield' => '-id',
          'noChildren' => true,
          'icon' => 'files-o',
        ],
      ],
    ]);
  }
}
