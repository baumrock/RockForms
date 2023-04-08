# Hooks

RockForms extends Nette Forms with the following hooks:

```php
RockForms::renderField
RockForms::renderFieldMulti
RockForms::renderFields
```

All hookable methods have the `Renderer` as their first argument and if you need you can access the `$form` from that renderer:

```php
$wire->addHookAfter("RockForms::renderField", function($event) {
  bd($event);
  $renderer = $event->arguments(0);
  $form = $renderer->getForm();
  $control = $event->arguments(1);
  ...
});
```

Use TracyDebugger's `bd($event)` to inspect the event object for the other hooks:

<img src=bd.png class=blur alt="Tracy Dump">

## Modify the label of all optional fields

This example shows how you can make every required field's label bold and add the note `optional` to every field that is not required. Note that NetteForms uses the terms `label` and `caption` for field labels.

[rockforms=Optional]

`label: /site/ready.php`
```php
$wire->addHookBefore("RockForms::renderField", function (HookEvent $event) {
  $control = $event->arguments(1);
  if ($control->isRequired()) {
    // make required fields bold
    $control->getLabelPrototype()->addClass('uk-text-bold');
  } else {
    // add optional note if not required
    $renderer = $event->arguments(0);
    $label = $control->label->getText() . " <small>(optional)</small>";
    $control->setCaption($renderer->html($label));
  }
});
```

If you want that changes to apply only to one specific form you can place the hook in the `init()` method of your form:

```php
<?php

namespace RockForms;

class Optional extends RockForm
{

  public function init()
  {
    // hook goes here
    // instead of $wire->... use $this->wire->...
  }

  public function buildForm()
  {
    $this->addText('forename', 'Forename')
      ->setRequired();
    $this->addText('surname', 'Surname');
  }
}
```
