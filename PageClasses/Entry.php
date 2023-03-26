<?php

namespace RockForms;

use ProcessWire\Inputfield;
use ProcessWire\Page;
use ProcessWire\WireData;
use RockMigrations\MagicPage;

class Entry extends Page
{
  use MagicPage;
  use RockFormsPage;

  const tpl = "rockforms_entry";
  const prefix = "rockforms_entry_";

  const field_form = self::prefix . "form";
  const field_values = self::prefix . "values";
  const field_labels = self::prefix . "labels";
  const field_user = self::prefix . "user";

  public function __construct()
  {
    $tpl = $this->wire->templates->get(self::tpl);
    if ($tpl) $this->template = $tpl;
    $this->parent = $this->rockforms()->entriesPage();
    parent::__construct();
  }

  /** magic */

  public function onCreate()
  {
    $this->status = 1;
    $this->name = $this->wire->pages->names()->uniqueRandomPageName([
      'min' => 30,
      'max' => 50,
    ]);
    $this->set(self::field_user, $this->wire->user->id);
  }

  public function editForm($form)
  {
    $this->removeSaveButton($form);
  }

  public function editFormContent($form)
  {
    $userid = $this->getFormatted(self::field_user);
    if ($f = $form->get(self::field_form)) {
      $f->columnWidth = 25;
    }
    $form->add([
      'type' => 'markup',
      'label' => 'Submitted by',
      'icon' => 'user-circle-o',
      'value' => $this->wire->users->get($userid)->name ?: $userid,
      'columnWidth' => 25,
    ]);
    $form->add([
      'type' => 'markup',
      'label' => 'Submitted at',
      'icon' => 'clock-o',
      'value' => date("Y-m-d H:i:s", $this->created),
      'columnWidth' => 25,
    ]);

    $confirm = $this->confirm();
    $form->add([
      'type' => 'markup',
      'label' => 'Confirmed',
      'icon' => 'sign-in',
      'value' => $this->rockforms()->checkbox($confirm, true)
        . " " . ($confirm ? date("Y-m-d H:i:s", $confirm) : ''),
      'columnWidth' => 25,
    ]);

    $form->add([
      'type' => 'markup',
      'label' => 'Form Data',
      'icon' => 'database',
      'value' => $this->rockforms()->renderTable(
        $this->getValues(),
        $this->labels(),
        true
      ),
      'notes' => 'Use ->getValues() to access these values or ->getValue("foo") to get a single value.',
    ]);

    $form->add([
      'type' => 'markup',
      'label' => 'Confirmation',
      'icon' => 'link',
      'value' => $this->rockforms()->renderTable([
        'Key' => "<div class='uk-text-truncate'>{$this->name}</div>",
        'Confirmation-Link' => "<a href='{$this->confirmLink()}' target=_blank>{$this->confirmLink()}</a>",
      ]),
      'notes' => "You can implement the method onConfirm() in {$this->form()} to define custom logic or redirects after the opt-in page has been viewed.",
      'collapsed' => Inputfield::collapsedYes,
    ]);
  }

  /** backend */

  public function badge($str, $class = "uk-text-muted")
  {
    return "<span
      class='uk-text-small uk-margin-small-right uk-background-muted $class'
      style='padding: 2px 10px; border-radius: 5px; display:inline-block;font-variant-numeric: tabular-nums; font-size:11px;'
      >$str</span>"; // x
  }

  /**
   * Get form from current entry
   */
  public function getForm()
  {
    $name = $this->form();
    return $this->rockforms()->getForm($name);
  }

  public function getPageListLabel()
  {
    return $this->badge(date("Y-m-d H:i:s", $this->created))
      . $this->badge($this->form(), '')
      . $this->title;
  }

  public function getValue($name)
  {
    return $this->getValues()->get($name);
  }

  public function getValues()
  {
    $arr = json_decode($this->getFormatted(self::field_values), true);
    $data = $this->wire(new WireData());
    if (!is_array($arr)) return $data;
    $data->setArray($arr);
    return $data;
  }

  public function labels()
  {
    $arr = json_decode($this->getFormatted(self::field_labels), true);
    $data = $this->wire(new WireData());
    if (!is_array($arr)) return $data;
    $data->setArray($arr);
    return $data;
  }

  public function migrate()
  {
    $rm = $this->rockmigrations();
    $rm->migrate([
      'fields' => [
        self::field_form => [
          'type' => 'text',
          'label' => 'Form',
          'icon' => 'paper-plane-o',
          'textformatters' => [
            'TextformatterEntities',
          ],
          'collapsed' => Inputfield::collapsedNoLocked,
        ],
        self::field_values => [
          'type' => 'textarea',
          'label' => 'Form Values',
          'rows' => 5,
          'icon' => 'align-left',
          'collapsed' => Inputfield::collapsedHidden,
        ],
        self::field_labels => [
          'type' => 'textarea',
          'label' => 'Field Labels',
          'rows' => 5,
          'icon' => 'align-left',
          'collapsed' => Inputfield::collapsedHidden,
        ],
        self::field_user => [
          'type' => 'integer',
          'label' => 'User',
          'icon' => 'user-circle-o',
          'collapsed' => Inputfield::collapsedHidden,
        ],
      ],
      'templates' => [
        self::tpl => [
          'fields' => [
            'title' => [
              'required' => false,
              'collapsed' => Inputfield::collapsedHidden,
            ],
            self::field_form,
            self::field_user,
            self::field_labels,
            self::field_values,
          ],
          'noSettings' => true,
          'noParents' => true,
        ],
      ],
    ]);
  }

  public function confirm($val = null)
  {
    if ($val === null) return $this->meta('opt-in');
    if ($val === true) $val = time();
    $this->meta('opt-in', $val);
  }

  public function confirmLink()
  {
    return $this->wire->pages->get(1)->httpUrl(true)
      . $this->rockforms()->confirmParam . "/" . $this->name . "/";
  }
}
