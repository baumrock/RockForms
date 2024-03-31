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

This is a security feature from Nette, which is a good thing. It prevents possible injection attacks. But unfortunately it's too complicated to add HTML if you know what you are doing.

But RockForms has you covered! Simply wrap your markup with `$form->html()`:

```php
$form->addCheckbox(
  "gdpr",
  $form->html("I agree to the <a href=#>privacy policy</a>")
);
```

<img src=checkbox2.png class=blur alt="Label Markup">

Always make sure that you never pass unsanitized markup to `$form->html()` for security reasons!
