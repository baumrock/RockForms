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
      'title' => 'RockForms Management Module',
      'version' => '0.0.1',
      'summary' => '',
      'icon' => 'th-list',
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

    $form->add([
      'type' => 'text',
      'name' => 'createform',
      'label' => 'Create a New Form',
      'notes' => 'Enter the name of your form and hit enter to submit',
      'icon' => 'plus',
    ]);
    $f = $form->children()->last();
    if ($createError) $f->error($createError);

    return $form->render();
  }

  private function rockforms(): RockForms
  {
    return $this->wire->modules->get('RockForms');
  }
}
