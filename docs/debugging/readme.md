# Debugging

RockForms adds several helpers to make your forms more secure and more reliable. These hidden gems can sometimes cause problems, for example a browser might fill a hidden honeypot field and thus the form will not submit.

You can debug these kind of issues by enabling debug mode for the form and showing all fields:

```php
public function buildForm()
{
  $form = $this;
  $form->setRockFormsRenderer('UIkit');
  $form->debug(); // add this line
  // ...
}
```
