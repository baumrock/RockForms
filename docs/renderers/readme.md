# Form Renderers

When working with a CSS framework (like UIkit, Bootstrap or even TailwindCSS) forms usually have predefined classes to make them look nicer by default.

You could apply all these classes at runtime ([see docs here](../markup/)), but it's a lot easier if you have a renderer that does that for you.

## Using the UIkit Renderer

```php
$form->setRockFormsRenderer("UIkit");
```

<div class="uk-alert uk-alert-warning">Warning: Make sure to add fields AFTER setting the renderer as the renderer will reset your form's fields!</div>

## Creating your own Renderer

You can very easily create your own renderers for your project. Simply place it in `site/templates/RockForms/Renderer` and RockForms will automatically load it for you once you use it. Here is a simple example to get you startet - please also see `site/modules/RockForms/RockFormsRenderer/UIkit.php` as inspiration!

```php
<?php

namespace RockForms\Renderer;

use Nette\Forms\Controls\TextInput;
use Nette\Forms\Form;

class Demo extends RockFormsRenderer
{
  public function __construct()
  {
    parent::__construct();

    // see RockFormsRenderer for all other wrapper settings
    $this->wrappers['error']['item'] = 'div style="color: red"';
  }

  /**
   * Provides complete form rendering.
   * @param  string  $mode  'begin', 'errors', 'ownerrors', 'body', 'end' or empty to render all
   */
  public function render(Form $form, ?string $mode = null): string
  {
    foreach ($form->getControls() as $control) {
      if ($control instanceof TextInput) {
        $control->getControlPrototype()
          ->addClass('my-text-class');
      }
    }

    return parent::render($form, $mode);
  }
}
```

To use this renderer in your form do this:

```php
class Contact extends RockForm
{

  public function buildForm()
  {
    $form = $this;
    $form->setRockFormsRenderer('Demo');
    // ...
  }
}
```
