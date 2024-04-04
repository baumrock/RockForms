<?php

namespace ProcessWire;

/**
 * @author Bernhard Baumrock, 12.07.2023
 * @license COMMERCIAL PLEASE DO NOT DISTRIBUTE
 * @link https://www.baumrock.com
 */
class ProcessRockForms extends Process
{
  const permission = "rockforms-gui";

  public static function getModuleInfo()
  {
    return [
      'title' => 'RockForms GUI',
      'version' => '1.0.0',
      'summary' => 'GUI for creating forms and managing form entries.',
      'icon' => 'paper-plane-o',
      'requires' => [
        'RockForms',
      ],
      'installs' => [],
      'permission' => self::permission,
      'permissions' => [self::permission => 'May run the RockForms GUI module'],

      // page that you want created to execute this module
      'page' => [
        'name' => 'rockforms',
        'parent' => 'setup',
        'title' => 'RockForms',
        'icon' => 'th-list',
      ],
    ];
  }

  public function init()
  {
    parent::init(); // always remember to call the parent init
  }

  private function createForm()
  {
    $name = $this->wire->input->post('createform', 'string');
    if (!$name) return;
    $name = ucfirst($this->wire->sanitizer->camelCase($name));
    if (!$name) return "Invalid name - please try another one!";

    // only allow creating forms in debug mode
    if (!$this->wire->config->debug) {
      return "Creating forms is only allowed when \$config->debug = TRUE";
    }

    if (!$this->wire->user->isSuperuser()) {
      return "Creating forms is only allowed for superusers.";
    }

    $rf = $this->rockforms();
    $form = $rf->getForm($name, true);
    if ($form) return "Form $name already exists - please choose another name!";

    // copy stubfile to /site/templates
    $content = $this->wire->files->fileGetContents(__DIR__ . "/stubs/Form.txt");
    $content = str_replace('{name}', $name, $content);
    $dst = $this->wire->config->paths->templates . "RockForms";
    $this->wire->files->mkdir($dst);
    $file = "$dst/$name.php";
    $this->wire->files->filePutContents($file, $content);
    $this->created = $file;
  }

  /**
   * Main GUI
   */
  public function execute()
  {
    $createError = $this->createForm();

    $this->headline('RockForms');
    $this->browserTitle('RockForms');
    /** @var InputfieldForm $form */
    $form = $this->wire->modules->get('InputfieldForm');

    if ($file = $this->created) {
      $name = substr(basename($this->created), 0, -4);
      $form->add([
        'type' => 'markup',
        'label' => 'Created Form ' . $name,
        'icon' => 'check',
        'value' => "Created a new boilerplate file at <strong>$file</strong> - please check that file and modify the code to your needs.
          <br><br>On the frontend you can display your form like this:" .
          '<pre><code>echo $rockforms->render("' . $name . '");</code></pre>',
      ]);
    }

    if ($this->wire->config->debug && $this->wire->user->isSuperuser()) {
      $form->add([
        'type' => 'text',
        'name' => 'createform',
        'label' => 'Create a New Form',
        'notes' => 'Enter the name of your form and hit enter to submit',
        'icon' => 'plus',
        'collapsed' => Inputfield::collapsedYes,
      ]);
      $f = $form->children()->last();
      if ($createError) $f->error($createError);
    } elseif ($this->wire->user->isSuperuser()) {
      $form->add([
        'type' => 'markup',
        'label' => 'Create a New Form',
        'icon' => 'plus',
        'value' => 'Creating forms is only allowed if $config->debug = TRUE',
        'notes' => 'This field is only shown to superusers.',
        'collapsed' => Inputfield::collapsedYes,
      ]);
    }

    // load the pagelist javascript
    $this->wire->modules->get('ProcessPageList');
    $rockformsroot = $this->wire->pages->get('/rockforms');
    $form->add([
      'name' => 'rockformsroot',
      'type' => 'markup',
      'label' => 'Manage Form Entries',
      'icon' => 'paper-plane-o',
      'value' => "
        <style>
        .PageListTemplate_rockforms_entry .PageListActionLock,
        .PageListTemplate_rockforms_entry .PageListActionMove {
          display: none !important;
        }
        </style>
        <div id='rockformsroot'></div>
        <script>
        $('#rockformsroot').ProcessPageList({
          rootPageID: $rockformsroot,
          showRootPage: false,
        });
        </script>",
    ]);

    if ($this->wire->user->isSuperuser()) {
      $url = $this->wire->pages->get(2)->url .
        'setup/logs/view/rockforms-spam/';
      $form->add([
        'type' => 'markup',
        'label' => 'Spam',
        'icon' => 'ban',
        'value' => "Please see the logs <a href=$url>here</a>.",
        'collapsed' => Inputfield::collapsedYes,
        'notes' => 'This field is only shown to superusers.',
      ]);
    }

    return $form->render();
  }

  private function rockforms(): RockForms
  {
    return $this->wire->modules->get('RockForms');
  }
}
