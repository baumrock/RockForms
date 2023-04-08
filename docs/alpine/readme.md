# Alpine JS

[rockforms=Alpine]

## Code

```php
$form->rockfrontend()->scripts()->add(
  "https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js",
  "defer"
);
$form->setHtmlAttribute("x-data", "{name: 'Bernhard'}");
$form->addMarkup("
  <div class='uk-margin'>
    You have a lovely name, <strong x-text='name'></strong>!
  </div>
");
$form->addText("name", "Enter your name")
  ->setHtmlAttribute('x-model', 'name');
```