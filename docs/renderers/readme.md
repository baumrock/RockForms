# Form Renderers

When working with a CSS framework (like UIkit, Bootstrap or even TailwindCSS) forms usually have predefined classes to make them look nicer by default.

You could apply all these classes at runtime ([see docs here](../markup/)), but it's a lot easier if you have a renderer that does that for you.

## Using the UIkit Renderer

```php
$form->setRockFormsRenderer("UIkit");
```

<div class="uk-alert uk-alert-warning">Warning: Make sure to add fields AFTER setting the renderer as the renderer will reset your form's fields!</div>

## Creating your own Renderer

Please see `site/modules/RockForms/RockFormsRenderer/UIkit.php` as an example how to create a custom renderer.
