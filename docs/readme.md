# Powerful and Secure Forms with Live-Validation and 100% Custom Markup

RockForms is based on the great [Nette Forms Component](https://doc.nette.org/en/forms) and adds a little ProcessWire magic here and there to make working with forms a breeze.

It will render forms directly into the markup of your website (no iframes!), which makes it a perfect companion for tools like [HTMX](https://htmx.org/) or [Alpine.js](https://alpinejs.dev/) ❤️

Rendering forms is as simple as this:

```php
echo $rockforms->render('MyForm');
```

## Working Example

In this example we will build the following form.

[rockforms=Quickstart]

- Try to submit an empty form

  You will see that the form will not submit and show an error message! The interesting part here is that this validation happens on the client side without ever hitting the server. If anybody ever tried to manipulate your form via Devtools, no worries, the validation will also happen on the server side as well!

- Try to submit the form with given name "baz"

  You will see that the form submits, but it will show an error. This is an example to demonstrate that server-side validation is also possible (for example validating sensitive information without exposing business secrets).

  Another great thing is that even when the form submission was not successful all inputfield values will be restored! Have you ever submitted a long form and then got an error message saying "please try it again" and all input was lost? Uncool! This will not happen when using RockForms.

## Now let's get to the code!

First, we define the fields of our form:

```php
$form
  ->addText("forename", "Enter your first name")
  ->setRequired("We need your first name to show it!");
$form
  ->addText("surname", "Enter your given name");
$form
  ->addSubmit("submit", "Submit your name");
```

Now our form knows which fields to show, great. The next thing is to define what it should do when the form is processed - for example it could send an E-Mail, it could create a User, it could create an Ivoice, whatever. Imagination and your PHP skills are the limit 😉

```php
$values = $form->getValues();
if ($values->surname == 'baz') {
  $form['surname']->addError('Sorry, baz is not allowed as given name.');
}
// otherwise send an email
// or create an invoice etc...
```

Ok, great, now the form knows what to do after submit. So what if everything went as expected - we want to show the user a success message, right?

```php
$name = $values->forename;
return "<div class='uk-alert uk-alert-success'>Thank you for submitting the form, $name!</div>";
```

That's all there is to know for building great forms when using RockForms!

If you are wondering where to put this code, head over to the next chapter, but don't worry - RockForms offers a GUI to create your form boilerplate 😉😎😅

## Examples

You can find all examples of this docs in the `/docs/.examples` folder of the module.
