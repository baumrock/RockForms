# Custom Markup

You can customise every aspect of your forms. To understand what you need to do it is important that you understand how NetteForms works and renders itself.

On this page we will cover how to customize markup during runtime. Please see also the page about [Form Renderers](../renderers/) for advanced usage.

## Anatomy of Nette Forms

<img src=nette.webp class=blur alt="Nette Forms Anatomy">

As you can see every Form consists of the form root element and that element holds all `Form Controls` aka fields.

If you look at the blue box you see that each form control consists of the `label` and the `control`. The latter we'd call `Inputfield` in ProcessWire - that is the `<input>` or `<select>` etc. in HTML.

Please see the Nette docs about form rendering [here](https://doc.nette.org/en/forms/rendering#toc-without-latte).

## Adding Classes to your Form

To get your form Element and manipulate its properties you can do this:

```php
public function buildForm()
{
  // get the Nette object that represents the form
  $form = $this->getElementPrototype();
  // Nette provides the addClass method
  $form->addClass('uk-grid-small');
}
```

## Adding Classes to your Fields

To add classes to your fields the process is similar. We use the same `addClass` method, but we apply it to the `control` of your field:

```php
public function buildForm()
{
  ...
  $field = $this->addSubmit('submit', 'OK');
  $field->getControlPrototype()->addClass('uk-width-1-1');
}
```

This can also be written like this:

```php
public function buildForm()
{
  ...
  $this->addSubmit('submit', 'OK')
    ->getControlPrototype()
    ->addClass('uk-width-1-1');
}
```

## Adding Classes to Labels

To access the label Nette provides the `getLabelPrototype()` method:

```php
public function buildForm()
{
  ...
  $this->addText('demo', 'I have a red border')
    ->getLabelPrototype()
    ->addClass('my-class')
    ->setAttribute('style', 'border: 2px solid red');
}
```

## Adding/setting Attributes

As you can see in the example above there is not only the `addClass` method but also `setAttribute` and [many more](https://api.nette.org/utils/master/Nette/Utils/Html.html).

## Adding Custom HTML
