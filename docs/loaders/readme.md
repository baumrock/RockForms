# RockLoaders

By default, RockForms will use RockLoaders to show a loader animation when the form is submitted. The default loader is "dots", which grays out the background of your page and shows white animated dots. This animation is neutral and should work well in most cases.

## Adding custom loaders

You can add custom loading animations to all of your forms, for example you could use the `email` loader for a contact form to give your site a little extra touch.

To do so you need to attach the loader in your `ready.php` file:

```php
// site/ready.php
rockloaders()->add('email');
```

And then you need to tell your form to use the new loader. This is done by adding the `rockloader` attribute to your form's HTML element:

```php
// in your form's php file in the buildForm() method
$form->getElementPrototype()
  ...
  ->setAttribute("rockloader", "email");
```
