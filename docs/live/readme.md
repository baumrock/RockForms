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
  // load RockForms first
  ->add('/site/modules/RockForms/dst/RockForms.min.js')
  // then load custom options
  ->add('/site/templates/scripts/liveformoptions.js')
  // finally load live validation
  ->add('/site/modules/RockForms/dst/live-form-validation.min.js')
  // ...
```
