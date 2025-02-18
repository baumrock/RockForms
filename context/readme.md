# Context Feature

The Context Feature allows you to customize how forms are rendered by providing additional configuration data. This is particularly useful when integrating forms with RockPageBuilder or when you need to adjust the form's appearance and behavior based on where it's being used.

## Usage with RockPageBuilder

A common use case is within RockPageBuilder blocks where you can:

1. Select which form to display
2. Customize the form's appearance through context settings
3. Configure block-specific behavior

### Example Context Settings

- Button styling (color, size, alignment)
- Form layout and spacing
- Custom CSS classes
- Success/error message customization
- Form-specific redirects

## Implementation

When using forms in a RockPageBuilder block, you can pass the entire block as context data. This automatically makes all block settings available to the form renderer:

```php
// The block object contains all your block settings like colors, sizes, etc.
$rockforms->render($block->formName(), $block);
```

This simple approach allows you to:
- Use all block settings as form context
- Customize the form appearance through the block's fields
- Maintain clean, reusable code

The form will automatically receive all context data from your block settings, which you can configure in your RockPageBuilder block fields.

## Accessing Context Data

Context data is accessible within your form class through the `context` property of the `RockForm` instance. You can use this data to customize your form's behavior and appearance:

```php
// Inside your form class
public function buildForm() {
  // Get block from context
  $block = $this->context;

  // get custom class from block settings
  $cls = $block->settings('buttonClass');

  // add submit button with
  $this->addSubmit("submit", $label)
      ->getControlPrototype()
      ->addClass($cls);
}
```
