# Hooks

Not everything that glitters is gold.

When coming from the shiny world of ProcessWire, sometimes working with NetteForms can be a little complicated...

That's why RockForms extends Nette Forms with the following hooks:

```php
RockForms::renderField
RockForms::renderFieldMulti
RockForms::renderFields
```

<div class="uk-alert uk-alert-warning">Note: You can also use RockFrontend's DOM-Tools for all kinds of markup manipulation and might find the syntax easier. See docs about <a href=../markup>custom markup</a>.</div>

## Why

Many customisations can be done directly from within your `buildForm()` method, but sometimes you want to make global changes that apply to all forms. For example you could want to make all required fields <strong>bold</strong> and show an `(optional)` note on all non-required fields.

In Nette you'd create a <a href=https://doc.nette.org/en/forms/rendering#toc-renderer>custom renderer</a> for this, but it's really not that easy to do as you have to understand all the inner workings of NetteForms.

In ProcessWire, we are used to change every little detail with just a few lines of code in the appropriate hook, so RockForms brings that concept to NettForms!

## Principle

```php
$wire->addHookAfter("RockForms::renderField", function($event) {
  // dump the event to tracy for debugging
  bd($event);

  // get the renderer that is used to render this form
  $renderer = $event->arguments(0);

  // get the form that is rendered
  $form = $renderer->getForm();

  // get the field that is rendered
  // in nette forms a field is called "control"
  $field = $event->arguments(1);

  // ...
});
```

Use TracyDebugger's `bd($event)` to inspect the event object for the other hooks:

<img src=bd.png class=blur alt="Tracy Dump">

## Example

This example shows how you can make every required field's label bold and add the note `optional` to every field that is not required. Note that NetteForms uses the terms `label` and `caption` for field labels.

[rockforms=Optional]

`label: /site/ready.php`
```php
$wire->addHookBefore(
  "RockForms::renderField",
  function (HookEvent $event) {
    // get the field that is rendered
    // in netteforms its called "control"
    $control = $event->arguments(1);

    // make required fields bold
    if ($control->isRequired()) {
      $control->getLabelPrototype()->addClass('uk-text-bold');
    }
    // add optional note if not required
    else {
      $renderer = $event->arguments(0);
      $label = $control->label->getText() . " <small>(optional)</small>";
      $control->setCaption($renderer->html($label));
    }
  }
);
```

Unfortunately the Nette API is not as straightforward as we are used to from ProcessWire. But inside the hook we are directly working with Nette objects and so I can't do anything about the syntax. Please see the docs about NetteForms <a href=https://doc.nette.org/en/forms/standalone>here</a>.

To understand the basics of NetteForms rendering see this illustration:

<img src=../form-areas-en.webp class=blur>

Also check out the docs about <a href=../markup>custom markup</a>.

## CSS Frameworks

To apply CSS Framework specific classes to all your form elements the best is to create a custom renderer. It's not that hard to do if you just copy the UIkit renderer that comes with RockForms and then change the appropriate classes. See the <a href=../renderers>docs about Renderers</a> for details.
