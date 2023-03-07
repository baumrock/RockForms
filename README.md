# RockForms

ProcessWire module for simple, secure and versatile forms based on NetteForms.

RockForms registers the new API variable `$rockforms` that is available in all your template files or via `$this->wire->rockforms`.

Wording: RockForms (with s, plural) is the module and RockForm (without s, singular) is one instance of a `RockForm` object.

# Usage

```php
// create form with name "demo"
$form = new RockForm("demo");
$form->addText("forename", "Enter your first name")
  ->setRequired();
$form->addText("surname", "Enter your given name");
$form->addSubmit("submit", "Submit your name");

// render success message or form
if ($form->showSuccess()) {
  echo "Thank you for your order!";
} else echo $form->render();
```

## Form submission / form action

If you don't specify a form action (`$form->setAction('/foo/')`) rockforms will submit the form to the currently viewed page and set the success parameter that is configured in the module's settings.

By default this is `form-success` so a form on page `/foo/bar/` would submit to `/foo/bar/?form-success=demo` where `demo` is the required name that you gave your form.

Note that if you reload that page, RockForms will automatically redirect to the page url without the success parameter. This even works with multiple forms on the same page!

# Rendering forms

See https://doc.nette.org/en/forms/rendering for all details about nette forms rendering!

## Renderer

To better understand how NetteForms will render your form inspect the renderer your form is using:

```php
$renderer = $form->getRenderer();
bd($renderer); // using TracyDebugger!
```

<img src=https://i.imgur.com/ZwX1VNt.png height=400>

You can then modify the markup of all form elements by setting new wrapper templates:

```php
$renderer = $form->getRenderer();
$renderer->wrappers['controls']['container'] = 'dl';
$renderer->wrappers['pair']['container'] = null;
$renderer->wrappers['label']['container'] = 'dt';
$renderer->wrappers['control']['container'] = 'dd';
```

### Creating a reusable Renderer

Most likely you don't want to apply these changes to all of your forms manually, so you can create a custom Renderer that your forms can then use globally for rendering. Also using a custom Renderer you have a lot more control and options for rendering your forms!

## Live Validation
