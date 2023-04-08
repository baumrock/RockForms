# Creating Forms

There are two ways you can create your forms:

1. Direct output in a template file
1. As a custom PHP class (recommended!)

## Direct Output

You can simply create forms from within your ProcessWire template files:

`label: /site/templates/includes/footer.php`
```php
<footer>
  <?php
  $form = new RockForm("demo");
  $form->addText("forename", "Enter your first name");
  $form->addText("surname", "Enter your given name");
  $form->addSubmit("submit", "Submit your name");
  if ($form->showSuccess($values)) {
    echo "<strong>Thank you for submitting the form!</strong>";
  } else {
    echo $form->render();
  }
  ?>
</footer>
```

Note that this approach is limited though and I find it a lot better to place my forms in a dedicated file.

## Custom File

This is the recommended way because it provides a lot more structure and a lot more options. It also helps to make your project better maintainable!

You can create your file in any folder you like, but the default folder is `/site/assets/RockForms`. In this example we will create a simple `Newsletter` form:

`label: /site/assets/RockForms/Newsletter.php`
```php
<?php

namespace RockForms;

class Newsletter extends RockForm
{
}
```

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

Similar to the `showSuccess()` switch on direct output we have the `renderSuccess()` method in OOP style:

```php
public function renderSuccess($values)
{
  return "Thank you, {$values->name}! We will get back to you soon.";
}
```