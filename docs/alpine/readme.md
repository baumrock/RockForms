# Alpine JS

[rockforms=Alpine]

## Code

```php
public function buildForm()
{
  $this->rockfrontend()->scripts()->add(
    "https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js",
    "defer"
  );
  $this->setHtmlAttribute("x-data", "{name: 'Bernhard'}");
  $this->addMarkup("
    <div class='uk-margin'>
      You entered: <strong x-text='name'></strong>
    </div>
  ");
  $this->addText("name", "Enter your name")
    ->getControlPrototype()
    ->setAttribute('x-model', 'name');
}
```