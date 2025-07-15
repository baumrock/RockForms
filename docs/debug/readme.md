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

## Global debug mode

To enable global debug mode you can add this to your `/site/ready.php` or any other place that is executed on every request:

```php
rockforms()->debug();
```

## Logging Form Submissions

Sometimes it is also useful to log all form submissions to a logfile. This can be done by enabling the `logFormSubmissions` setting in the RockForms module config.

All form values will be logged as JSON string. This happens before `processInput()` and before any spam protection is applied, so it is useful for debugging only.

The log file can be found at `/site/assets/logs/form-submissions.txt` or you can inspect it via the ProcessWire admin interface.
