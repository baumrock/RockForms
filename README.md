# RockForms

ProcessWire module for simple, secure and versatile forms based on NetteForms.

RockForms registers the new API variable `$rockforms` that is available in all your template files or via `$this->wire->rockforms`.

# Usage

```php
$form = new RockForm("demo");
$form->addText("forename", "Enter your first name")
  ->setRequired();
$form->addText("surname", "Enter your given name");
$form->addSubmit("submit", "Submit your name");
if ($form->showSuccess()) {
  echo "Thank you for your order!";
} else echo $form->render();
```

## Form submission / form action

If you don't specify a form action (`$form->setAction('/foo/')`) rockforms will submit the form to the currently viewed page and set the success parameter that is configured in the module's settings.

By default this is `form-success` so a form on page `/foo/bar/` would submit to `/foo/bar/?form-success=demo` where `demo` is the required name that you gave your form.

Note that if you reload that page, RockForms will automatically redirect to the page url without the success parameter. This even works with multiple forms on the same page!
