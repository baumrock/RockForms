# Creating Forms

In RockForms every form is presented of one PHP file and is itself its own PHP class. If you are new to OOP, don't worry! RockForms has a GUI that creates that file for you:

<img src=rockforms.png class=blur>

Theoretically you can create your form files in any folder you like (for example in custom modules), but the GUI will place it in the default folder, which is `/site/assets/RockForms`.

A basic RockForm looks like this:

```php
<?php

namespace RockForms;

class Newsletter extends RockForm
{

  public function buildForm()
  {
    // field setup
  }

  public function processInput()
  {
    // form processing after submit
  }

  public function renderSuccess($values)
  {
    // render success message
  }
}
```

The boilerplate file that is created by RockForms when using the GUI comes with a lot of helpful comments as well!

### Adding Fields

This form does not have any fields yet, so we add them in the `buildForm()` method:

```php
<?php

namespace RockForms;

class Newsletter extends RockForm
{
  public function buildForm()
  {
    $this->addText('fieldname', 'Field Label');
    $this->addSubmit('ok');
  }
}
```

If you set up your IDE properly it will help you with creating your forms by listing all available methods:

<img src=newsletter.png class=blur alt="IDE Helper">

### Accessing Forms

Now that we have setup our form we can access it like this:

```php
$form = $rockforms->getForm("Newsletter");
```

When using a custom path you can get your form like this:

```php
$form = $rockforms->getFormFromFile("/path/to/Newsletter.php");
```

### Rendering Forms

You can either request your form and then render it:

```php
$form = $rockforms->getForm("Newsletter");
echo $form->render();
```

Or you use the shortcut:

```php
echo $rockforms->render("Newsletter");
```

### Success Message

```php
public function renderSuccess($values)
{
  return "Thank you, {$values->name}! We will get back to you soon.";
}
```
