# Processing Form Submissions

Processing form submissions is done in the `processInput` and `processSuccess` methods of your form. You can do whatever logic you need here.

## processSuccess()

This method is executed only if the form has no errors and is not spam. This is the perfect place for sending mails or saving entries to the database.

### saveEntry()

To save form submissions within your ProcessWire backend, you can utilize the `saveEntry()` method, ensuring that all data is securely stored and easily accessible for further processing or analysis.

```php
public function processSuccess()
{
  // get values (without system fields like CSRF)
  $values = $this->getValues(false);

   // use mail as title of page
  $this->saveEntry($values->mail);
}
```

The result is something like this:

<img src="save-entry.png" class="blur">

### Confirmation Links

Please see docs about <a href="../double/">Double Opt In</a>.

### Sending Mails

Just use the awesome ProcessWire API to send mail notifications upon form submissions:

```php
public function processSuccess()
{
  // get values without system fields like CSRF or honeypots
  $values = $this->values();

  // you can add additional information to your email by
  // adding custom runtime properties to your values!
  // here we add the "url" of the current page and show it in the email
  $values->url = $this->wire->page->httpUrl();

  // save entry to backend
  $this->saveEntry($values->mail);

  // notify
  $mail = new WireMail();
  $mail->to('office@yourcompany.com');
  $mail->to('office@yourclient.com');
  $mail->from('robot@yourwebsite.com');
  $mail->subject("Contact-Form submission on yourwebsite.com");
  $mail->replyTo($values->mail);
  $mail->bodyHTML($this->renderTable($values));
  $mail->send();
}
```

This will send an (admittedly ugly) email to you and your client. If you also want to send a confirmation to your client you can make your emails look nicer with custom HTML. RockMails can help here!

## processInput()

This method is executed on every form submission, even when the form has errors or is spam.

### Throwing Errors

In `processInput` and `processSuccess` you can add custom logic and throw custom arrows like this:

```php
public function processInput()
{
  // throw error on the form itself
  if($foo === 'bar') {
    $this->addError("Something is wrong");

    // prevent further execution by early exit if needed
    return;
  }

  // throw error on a single field aka component
  $values = $this->getValues();
  if ($values->surname === 'baz') {
    $this
      ->getComponent('surname')
      ->addError('--- name baz not allowed ---');
  }
}
```
