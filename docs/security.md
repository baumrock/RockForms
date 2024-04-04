# Security

RockForms is designed with security as a top priority, ensuring that forms are as secure as possible right out of the box. However, it's important to remember that the ultimate responsibility for security lies with the developer.

This is because RockForms cannot predict every context in which data will be used or determine which specific sanitization measures are appropriate for each use case. As a developer, you must ensure that you handle the data securely, applying the necessary sanitization and validation according to the context in which the data is used.

By prioritizing security features and providing developers with the tools to implement them effectively, RockForms ensures that your forms are not only functional but also secure against common threats. This section will guide you through the best practices for securing your forms and handling data responsibly.

## Basics

Before jumping into the details let's quickly see how a basic form submission looks like:

- User submits a form
- Before actually sending data to the server, the form will be validated on the client side by the live validation script provided by Nette.
- If client-side validation passes, the form data is then submitted to the server. The server uses Nette's Form validation to further validate the data.
- Next, the `processInput()` method of your form will be executed.
- When using `saveEntry()` data will be saved to the database.
- If no errors occurred a success message will be shown to the user that has submitted the form.

That means you have to be VERY careful whenever you

- save data to the database
- use data for custom actions (filters, selectors, sql queries, etc)
- use data for any kind of output (markup, text, success message, etc)

When using RockForms, you benefit from multiple layers of validation/security designed to protect your forms and the data they process.

## Client-Side

The first layer of validation comes from the live validation script provided by Nette. Before any data is sent to the server, RockForms leverages this script to ensure that the data entered by users meets the specified criteria. Unfortunately this layer can easily be bypassed by any experienced user that knows about browser devtools - so it's just a UX improvement and not a real security layer.

## Server-Side

Real security measures are implemented on the server side; it's crucial to sanitize all data submitted by users before saving or displaying it. This ensures the security of your application and prevents any potential data leaks.

### Nette Rules

The first layer of protection are Nette's validation rules that are applied to each form inputfield.

Let's say you want to ask the user for its email address. You could to this:

```php
$form->addText('email', 'Please enter your email address');
```

While this would work, the user could also enter `<script>alert(1)</script>`. Now let's assume that you output a list of all user emails in a backend application for logged in users. Now on that list you suddenly output an unwanted `<script>` tag that in this case just pops up an alert, but could easily also send sensitive information via `fetch()` to an untrusted destination.

So to make sure that users can only enter valid mail addresses you can use Nette's email field instead of a plain textfield:

```php
$form->addEmail('email', '...');
```

Now if you enter an invalid email address the form will not let you submit that input:

<img src=https://i.imgur.com/tlh8QT7.png class=blur width=400>

If you try to trick the form and disable client side validation it will also not work, because the server throws the same error on the backend.

Nette has plenty of validation rules that you can add to your fields, see their docs here: https://doc.nette.org/en/forms/validation#toc-rules

### Processing User Input

You process user input in the `processInput()` method of your form. There you can get values submitted by the user like this:

```php
// values as stored by nette
// not recommended!
$form->getValues();

// RockForms method to get values
// true means to also get anti-spam and CSRF input
$form->values(true);

// get raw values from user input
// skip internal fields like CSRF/anti-spam
$form->values(false);
```

Note that these values are only validated by nette forms so far. That means if you have a plain `text` input there is no validation at all other than the input is any kind of text.

We can't for example entity encode these values at this stage, because you might want to do something like this:

```php
if($values->company === "Baum & Rock") // ...
```

If that value was already entity encoded you'd have to compare with `Baum &amp; Rock` which is more than cumbersome. Another reason why we can't just entity encode those values is that you need to use the proper sanitizer for the given use case.

For example entity encoding the input would not help if you used the input in an SQL `limit` statement! In that case you'd need to typecast the input to an integer value (or even better use the `Numeric` validation rule on the input itself).

### Storing User Input

If you want to store user input in the database (typically by using the `saveEntry` method) you have to take care of not storing any malicious data or sensitive information in your database. What that actually means depends on your exact use case, but you can basically store anything in your database as long as you do proper sanitisation when using that data (eg for actions or output).

### Using User Input

User input can be used in many different ways and all those different use cases need different sanitisation.

- **Output**:
  When outputting user input you'll likely want to make sure that the data entered by the user is entity encoded, so that the user can't inject code into your websites markup.

- **SQL**:
  When doing any kind of SQL operations you have to be very careful to prevent SQL injection attacks. ProcessWire can help here by using the PW API instead of directly using SQL.

- **PW Selectors**:
  When using the PW API make sure to always sanitize input before using it in your selectors.

### renderSuccess

The `renderSuccess()` method is a little special! Since it's sole purpose is to output markup for the user after submitting the form we can apply the entities sanitizer for all values automatically, so we can't forget that.

