# setAction()

By default RockForms will submit forms with method `POST` to the same site they where rendered on.

If you want to change that behaviour you can set a custom action target as follows:

```php
public function buildForm() {
  $form = $this;
  $form->setAction("/contact-success/");
  // ...
}
```

Note that this approach might not work when using HTMX to submit your forms. In that case you can disable that feature:



This can help you improve the UX of your forms. See [here](https://processwire.com/talk/topic/27918-how-to-submit-forms-with-really-good-ux/) for an interesting discussion in the forum. 🤓
