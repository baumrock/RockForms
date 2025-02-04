# RockForms.js

If you want to use some features like HTMX or CSRF, the only requirement is to ensure that `RockForms.min.js` is loaded on your site. You can manually add the script tag to your site's HTML head section like so:

```php
echo $rockforms->scriptTag();
```

The output will look similar to the following:

```html
<script src="/site/modules/RockForms/assets/RockForms.min.js?sa179u" defer></script>
```

If you are wondering what that cryptic `?sa179u` part is: That's a cache-busting querystring that ensures that when RockForms is updated all your visitors will automatically load the new Version of RockForms and not load an outdated version from the browser's cache.

## RockDevTools

When using RockDevTools, you can add the `RockForms.min.js` to your assets list like so:

```php
// /site/templates/_init.php
if ($config->rockdevtools) {
  $devtools = rockdevtools();

  // ...

  // merge and minify JS files
  $devtools->assets()
    ->js()
    ->add('/site/templates/uikit/dist/js/uikit.min.js')

    // add rockforms assets
    ->add('/site/modules/RockForms/dst/RockForms.min.js')
    ->add('/site/modules/RockForms/dst/live-form-validation.min.js')

    // add your custom JS and save file
    ->add('/site/templates/scripts/main.js')
    ->save('/site/templates/dst/scripts.min.js');
}
```

The benefit of using RockDevTools is that you get a merged and minified JS file for your site, so you don't add an unnecessary additional HTTP request to your visitors.
