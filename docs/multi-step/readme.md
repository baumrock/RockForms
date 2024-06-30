# MultiStepForm

Creating multi-step-forms with RockForms is straightforward.

First, you need to add the steps:

```php
$form = new MultiStepForm();
$form->addSteps([
  'foo' => [
    'headline' => 'The foo step',
  ],
  'bar' => [
    'headline' => 'The bar step',
  ],
]);
```

Every Step added will be a `RockForms\Step` object which will have a property `form` that is automatically populated with the form name that will be rendered. In our example the rendered forms would be `Foo` and `Bar` (with a capital first latter).

## Adding a prefix

```php
$form = new MultiStepForm();
$form->prefix = 'Checkout';
$form->addSteps([
  'contact' => [
    'headline' => 'Your Contact Data',
  ],
  'payment' => [
    'headline' => 'Select payment method',
  ],
]);
```

In this example the rendered forms would be `CheckoutContact` and `CheckoutPayment`.
