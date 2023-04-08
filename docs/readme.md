# Powerful and Secure Forms with Live-Validation and 100% Custom Markup

RockForms is based on the great [Nette Forms Component](https://doc.nette.org/en/forms) and adds a little ProcessWire magic here and there to make working with forms a breeze.

## Quickstart

A very simple form could look like this:

```php
// create form with a unique name
$form = new RockForm("demo");

// field setup
$form
  ->addText("forename", "Enter your first name")
  ->setRequired("We need your first name to show it!");
$form
  ->addText("surname", "Enter your given name");
$form
  ->addSubmit("submit", "Submit your name");

// render output
if ($form->showSuccess($values)) {
  $name = $values->forename;
  if($values->surname) $name .= " " . $values->surname;
  echo "<strong>Thank you for submitting the form, $name!</strong>";
} else {
  echo $form->render();
}
```

This will render the following form (you can try to submit it)!

[rockforms=Quickstart]

## HTML

RockForms does NOT use iframes to render the form, so you have full control over the generated markup but without having to worry about all the complicated stuff 🚀

I have put a lot of effort into making the manipulation of the markup as easy as possible. You can add classes to any element and set attributes at runtime ([see docs](markup/)).

This makes RockForms a perfect companion for tools like [HTMX](https://htmx.org/) or [Alpine.js](https://alpinejs.dev/)
