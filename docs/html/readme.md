# HTML Helper

Sometimes you want to add HTML to your form elements, like adding a link to your privacy policy:

```php
$form->addCheckbox(
  "gdpr",
  "I agree to the <a href=#>privacy policy</a>"
);
```

This will render the following:

<img src=checkbox.png class=blur alt="Label Markup">

NetteForms makes it a little complicated to add custom HTML to their elements, but RockForms has you covered! Simply wrap your markup with `$form->html()`:

```php
$form->addCheckbox(
  "gdpr",
  $form->html("I agree to the <a href=#>privacy policy</a>")
);
```

  <img src=checkbox2.png class=blur alt="Label Markup">
