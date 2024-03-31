# AlpineJS

RockForms ❤️ AlpineJS

Alpine works by adding custom tags to your markup and that works perfectly together with RockForms, as you have full control over the markup of your forms and you can add custom attributes very easily.

See this example that updates the greeting message instantly while you are typing:

[rockforms=Alpine]

## Code

Traditionally we'd have to write some JavaScript in another file and listen for events and then update the DOM. This is all done by Alpine with just a little custom HTML:

```php
// first we need to load Alpine.js
// if you are not using RockFrontend simply add the <script> tag to your <head>
$form->rockfrontend()->scripts()->add(
  "https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js",
  "defer"
);

// now we turn the <form> into an alpine component using the x-data attribute
// we initialise it with the name "Bernhard"
$form->setHtmlAttribute("x-data", "{name: 'Bernhard'}");

// now we add a regular text field and bind its value to the "name" property of the component
$form->addText("name", "Enter your name")
  ->setHtmlAttribute('x-model', 'name');

// and finally we add some custom markup to the form
// where we use x-text that will always show the value of the "name" property of our component
$form->addMarkup("
  <div class='uk-margin'>
    You have a lovely name, <strong x-text='name'></strong>!
  </div>
");
```