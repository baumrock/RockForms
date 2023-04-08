# setAction()

By default RockForms will submit forms with method `POST` to the same site they where rendered on.

If you want to change that behaviour you can set a custom action target as follows:

```php
$form = new RockForm('demo');
$form->setAction("/contact-success/");
...
```

This can help you improve the UX of your forms. See [here](https://processwire.com/talk/topic/27918-how-to-submit-forms-with-really-good-ux/) for an interesting discussion in the forum. 🤓
