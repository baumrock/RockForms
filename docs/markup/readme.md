# Custom Markup

You can customise every aspect of your forms. On this page we will cover how to customize markup during runtime. Please see also the page about [Form Renderers](../renderers/) for advanced usage.

## Adding Custom HTML

You can add any HTML to your form at any place you want. RockForms comes with a custom control type that helps you with doing so. This is similar to the concept of runtime markup fields in ProcessWire:

```php
$form->addMarkup("<h2>foo bar</h2>");
```

### Using Field Tags

Using field tags you can quickly and easily customize the markup of your form's fields without creating a custom Renderer for your form. An example could be that we want to show some fields side by side on larger screens:

[rockforms=Grid]

```php
$form->setRockFormsRenderer("UIkit");
$form->addText('forename', 'Forename');
$form->addText('surname', 'Surname');
$form->addMarkup("
  <div class='uk-child-width-1-2 uk-grid-small' uk-grid>
    <div>{forename}</div>
    <div>{surname}</div>
  </div>
");
$form->addText('full', 'Full Width');
```

## Working with Form Elements

To manipulate single elements of your forms (like labels or input fields) you can interact with Nette's Html objects. To understand what you need to do it is important that you understand how Nette Forms work and get rendered.

### Anatomy of Nette Forms

<img src=nette.webp class=blur alt="Nette Forms Anatomy">

As you can see every Form consists of the form root element and that element holds all `Form Controls` aka fields.

If you look at the blue box you see that each form control consists of the `label` and the `control`. The latter we'd call `Inputfield` in ProcessWire - that is the `<input>` or `<select>` etc. in HTML.

Please see the Nette docs about form rendering [here](https://doc.nette.org/en/forms/rendering#toc-without-latte).

## Adding Classes to your Form

To get your form Element and manipulate its properties you can do this:

```php
$formElement = $form->getElementPrototype();
$formElement->addClass('uk-grid-small');
```

## Adding Classes to your Fields

To add classes to your fields the process is similar. We use the same `addClass` method, but we apply it to the `control` (aka `<input>` `<select>` ... element) of your field:

```php
$form->addSubmit('submit', 'OK')
  ->getControlPrototype()
  ->addClass('uk-width-1-1');
```

## Adding Classes to Labels

To access the label Nette provides the `getLabelPrototype()` method:

```php
$form->addText('demo', 'I have a red border')
  ->getLabelPrototype()
  ->addClass('my-class');
```

## Adding/setting Attributes

There is not only the `addClass` method but also `setAttribute` and [many more](https://api.nette.org/utils/master/Nette/Utils/Html.html) that you can use on any Nette Html object.

`label: Long version`
```php
$form->addText('demo', 'I have a red border')
  ->getLabelPrototype()
  ->setAttribute('border', '2px solid red');
```

Setting the text of the label is a little special:

```php
$form->addText('demo', 'My old label')
  ->setCaption('My new label');
```

For setting html attributes there is a shortcut that you can use directly on the field definition. So instead of `->getLabelPrototype()->setAttribute()` you can use `->setHtmlAttribute()`:

`label: Shortcut`
```php
$this->addText('demo', 'I have a red border')
  ->setHtmlAttribute('border', '2px solid red');
```
