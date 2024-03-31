# Form Validation

## Error Messages

You can either define custom error messages on a field level:

```php
$form
  ->addText('demo')
  ->setRequired('This field is required - enter something useful');
```

Or you can set global error messages in `/site/ready.php` like this:

```php
// use default german translations
$rockforms->setErrors('de');

// use custom translations
$rockforms->setErrors([
  Nette\Forms\Form::FILLED => 'Fill this out, you lazybones!',
]);
```

## Live Validation

[rockforms=Quickstart]

Click on the first inputfield, leave it blank and then click on the second field. The required message will show up immediately!

As soon as you fill the first field the error message will disappear. All that happens on the client side - without submitting the form - even though we set up our form solely on the server side!

That's some great Nette magic 😎
