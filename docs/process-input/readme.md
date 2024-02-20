# Processing Form Submissions

## Redirect on Success

## saveEntry

To efficiently handle form submissions within your ProcessWire site, you can utilize the `saveEntry` method. This method allows you to save the form submissions directly to the ProcessWire backend, ensuring that all data is securely stored and easily accessible for further processing or analysis.

```php
public function processInput()
{
  $values = $this->getValues();
  $this->saveEntry($values->mail);
}
```

The result is something like this:

<img src="save-entry.png" class="blur">

### Confirmation Links

To enhance GDPR compliance for newsletter opt-in forms, each entry is assigned a unique confirmation key. A generated link, associated with this key, is provided for the purpose of confirming the submission. When the recipient clicks on this link, a "confirmed" checkbox within the saved entry is marked as true, indicating their consent. It's important to note that this confirmation link is not dispatched automatically, allowing for flexible integration into your email communication strategy.

## Sending Mails

## Throwing Errors