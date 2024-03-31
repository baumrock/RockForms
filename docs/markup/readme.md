# Custom Markup

There are several ways how you can customize your forms markup:

- Using `$form->addMarkup(...)`
- Using [RockFrontend's DOM Tools](https://www.baumrock.com/en/processwire/modules/rockfrontend/docs/dom/#html)
- Using Nette API
- Using [Form Renderers](../renderers/)

## Using addMarkup()

You can add any HTML to your form at any place you want. RockForms comes with a custom control type that helps you with doing so. This is similar to the concept of runtime markup fields in ProcessWire:

```php
$form->addMarkup("<h2>foo bar</h2>");
```

### Using Field Tags

Using field tags you can quickly and easily customize the markup of your form's fields without creating a custom Renderer for your form. An example could be that we want to show some fields side by side on larger screens:

[rockforms=Grid]

```php
$form->setRockFormsRenderer("UIkit");

// first we add the fields
$form->addText('forename', 'Forename')
  ->setRequired('Example Error');
$form->addText('surname', 'Surname');

// then we add the custom markup
$form->addMarkup("
  <div class='uk-child-width-1-2 uk-grid-small' uk-grid>
    <div>{forename}</div>
    <div>{surname}</div>
  </div>
");

// other fields later
$form->addText('full', 'Full Width');
```

An important thing to note here is that `{forename}` does not only render the `<input>` of the field but also all necessary markup for validation. Try to focus the field and then move to another. The required error message will show up where you put your `{forename}` tag.

## Using RockFrontend's DOM Tools

RockFrontend has a great [DOM helper](https://www.baumrock.com/en/processwire/modules/rockfrontend/docs/dom/#html) that can manipulate any HTML markup in a jQuery-like fashion. This can also be great for manipulating the markup of your forms with an easy syntax.

In this example we will modify every `<input>` element - for quick testing you can place it in ready.php and then echo `$html` wherever you like (or dump it to tracy).

In this example we use the signup form for <a href=https://www.baumrock.com/rock-monthly>Rock Monthly</a> - if you are not signed up yet you are missing something every month ;)

`label: /site/ready.php`
```php
// this statement is used to get code intellisense
// so that your ide will help you finding the right syntax
use Wa72\HtmlPageDom\HtmlPageCrawler;

// first, get the raw html of the form
$html = rockforms()->render("RockMonthly");

// load html into RockFrontend's domtools
$dom = rockfrontend()->dom($html);

// now filter the dom for all <input> elements
// and then do something with every found node
$dom
  ->filter("input")
  ->each(function (HtmlPageCrawler $node) {
    $node->addClass("foo");
    $node->setAttribute("style", "border: 2px dashed black");
  });

// now save the new innerhtml back to the $html variable
$html = $dom->getInnerHtml();

// don't forget to echo the markup somewhere ;)
```

<img src=https://i.imgur.com/kqmHNwc.png class=blur>

## Using Nette API

To manipulate single elements of your forms (like labels or input fields) you can interact with Nette's Html objects. To understand what you need to do it is important that you understand how Nette Forms work and get rendered.

### Anatomy of Nette Forms

<img src=nette.webp class=blur alt="Nette Forms Anatomy">

As you can see every Form consists of the form root element and that element holds all `Form Controls` aka fields.

If you look at the blue box you see that each form control consists of the `label` and the `control`. The latter we'd call `Inputfield` in ProcessWire - that is the `<input>` or `<select>` etc. in HTML.

Please see the Nette docs about form rendering [here](https://doc.nette.org/en/forms/rendering#toc-without-latte).

### Adding Classes to your Form

To get your form Element and manipulate its properties you can do this:

```php
$formElement = $form->getElementPrototype();
$formElement->addClass('uk-grid-small');
```

### Adding Classes to your Fields

To add classes to your fields the process is similar. We use the same `addClass` method, but we apply it to the `control` (aka `<input>` `<select>` ... element) of your field:

```php
$form->addSubmit('submit', 'OK')
  ->getControlPrototype()
  ->addClass('uk-width-1-1');
```

### Adding Classes to Labels

To access the label Nette provides the `getLabelPrototype()` method:

```php
$form->addText('demo', 'I have a red border')
  ->getLabelPrototype()
  ->addClass('my-class');
```

### Adding/setting Attributes

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

## Using Form Renderers

Please see the docs about form renderers <a href=../renderers/>here</a>.

## Final note

I know the NetteForms API is not as straightforward as we are used to from ProcessWire, but luckily you don't need it most of the time (or at all).