# RockForms.js

Seamless integration at its finest! When you're using RockFrontend, `RockForms.js` will be loaded automatically without any extra setup. 😎

## Setup without RockFrontend

If you're not using RockFrontend and want to use some features like HTMX or CSRF, the only requirement is to ensure that `RockForms.min.js` is loaded on your site. You can manually add the script tag to your site's HTML head section like so:

```php
echo $rockforms->scriptTag();
```

The output will look similar to the following:

```html
<script src="/site/modules/RockForms/assets/RockForms.min.js?sa179u" defer></script>
```

If you are wondering what that cryptic `?sa179u` part is: That's a cache-busting querystring that ensures that when RockForms is updated all your visitors will automatically load the new Version of RockForms and not load an outdated version from the browser's cache.
