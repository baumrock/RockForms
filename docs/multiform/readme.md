# Multiple Forms

## Handling Multiple Instances of the Same Form on a Single Page

When using RockForms, each form is identified by a unique ID, which is derived from the form's name. For instance, a form named "Contact" will have an ID of "frm-Contact", and all its fields will be prefixed accordingly. This unique identification system is crucial for RockForms to correctly process form submissions and display the appropriate success messages.

However, this presents a challenge when you need to render the same form multiple times on a single page. A common scenario where this might occur is when displaying an "AddToCart" form for each product in a product list. To ensure that each form instance is correctly identified and processed by RockForms, it's essential to assign a unique ID to each form instance.

## Solution: Setting the "formName" Context

To address this issue, you can utilize the "formName" context when rendering the form. By setting a unique "formName" for each form instance, you can ensure that RockForms generates a unique ID for each one, thus allowing multiple instances of the same form to coexist on a single page without any conflicts.

Here's how you can set the "formName" context:

```php
foreach($products as $product) {
  $id = $product->id;
  // echo product markup here
  // then echo the custom form
  echo $rockforms->render("AddToCart", [
    'formName' => "frm-AddToCart-$id",
  ]);
}
```

Here's a breakdown of the example code:

1. A `foreach` loop iterates over an array of products.
2. Within the loop, each product's ID is retrieved and stored in the `$id` variable.
3. The `echo` statement then renders the form for each product by calling the `render` method on the `$rockforms` object.
4. The `render` method takes two arguments: The (initial) name of the form to render ("AddToCart") and an associative array that sets the context for the form.
5. The context array includes a key `formName` with a value that concatenates "frm-AddToCart-" with the product's ID, ensuring each form instance has a unique name.

By following this approach, you can effectively manage multiple instances of the same form on a single page, ensuring that each form submission is correctly processed by RockForms.
