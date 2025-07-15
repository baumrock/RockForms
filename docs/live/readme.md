# Live Validation

RockForms includes a powerful live validation feature that validates form inputs in real-time as users type. This provides immediate feedback to users without waiting for form submission.

## Setup

1. First, include the required JavaScript files in your template.

You can choose whatever tool you want - using RockDevTools it looks like this:

```php
// /site/templates/_init.php
$devtools->assets()
  ->js()
  // ...
  // load RockForms first
  ->add('/site/modules/RockForms/dst/RockForms.min.js')
  // then load live validation
  ->add('/site/modules/RockForms/dst/live-form-validation.min.js')
  // ...
```

## Styling Live Validation

You can set custom options for live validation by setting the `LiveFormOptions` global variable.

For this, create a file `/site/templates/scripts/liveformoptions.js` with content like this:

```js
var LiveFormOptions = {
  messageErrorClass:
    "uk-alert uk-alert-warning uk-margin-remove uk-display-block",
  messageErrorPrefix: "",
};
```

And add it to the list of loaded assets:

```php
// /site/templates/_init.php
$devtools->assets()
  ->js()
  // ...
  ->add('/site/modules/RockForms/dst/RockForms.min.js')
  ->add('/site/modules/RockForms/dst/live-form-validation.min.js')
  ->add('/site/templates/scripts/liveformoptions.js')
  // ...
```

## Script Loading Order

**Since version 2025-07-15**, RockForms automatically handles script loading order for live validation. The `live-form-validation.js` script is internally wrapped with a `setTimeout(0)` delay, which means:

- You can load your `LiveFormOptions` configuration **before** or **after** the live validation script
- Both scripts will execute in the correct order regardless of their load sequence
- **Important**: Avoid mixing immediate and deferred script loading - use the same loading strategy for both scripts

This flexibility eliminates common timing issues when setting up live form validation options.
