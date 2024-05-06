# File Uploads

<div class='uk-alert uk-alert-warning'>WARNING: Be very careful when adding file uploads to your form! Please read the safety notes of NetteForms carefully: https://doc.nette.org/en/forms/controls#toc-addupload</div>

## Adding Fields

First we need to add a file upload field to our frontend, which is easy:

```php
$form->addUpload("test", "test upload");
```

## Processing Uploads

Next, we need to process the upload and maybe save it to a ProcessWire page or send it via email.

### Automatic Uploads to Entry Pages

The easiest way is to use the `saveEntry()` feature for that, which will add all files that it finds in the submitted data to the `files` field of the entry.

All you have to do is this:

```php
public function processSuccess()
{
  // save entry to backend
  $values = $this->values();
  $entry = $this->saveEntry("Contact Form by {$values->email}");
}
```

### Sending Uploads via E-Mail

Now that we have our files on the system we can send them via email easily:

```php
public function processSuccess()
{
  // save entry to backend
  $values = $this->values();
  $entry = $this->saveEntry("Contact Form by {$values->email}");

  // send email
  $mail = new WireMail();
  // from, to, etc...
  foreach ($entry->files() as $file) {
    $mail->attachment($file->filename);
  }
  $mail->send();
}
```
