# HTMX

<div class="uk-alert uk-alert-warning">Needs <a href=../js/>RockForms.js</a></div>

HTMX transforms your traditional HTML forms into modern forms that use AJAX to submit data. This means you can update your webpage with a success message without reloading the page.

Here's what this approach offers:

- No need for full page reloads, enhancing the user experience.
- It prevents the same form from being submitted more than once.
- The form is only submitted if it passes frontend validation.

RockForms is designed to add as little extra load to your site as possible, ensuring it remains speedy. The HTMX library is loaded only when the user interacts with your form, keeping things efficient.

This is why RockForms will use HTMX for all forms by default. If you are already using HTMX on your site RockFrontend will not load it again.

## Disabling HTMX

If you don't want to use HTMX for your form you can set a flag like this:

```php
class Contact extends RockForm
{
  const HTMX = false;

  public function buildForm()
  {
    ...
  }
}
```

## Customizing HTMX Markup

The default setup for HTMX in RockForms is this:

```php
$form->setHtmlAttribute("hx-post", "./");
$form->setHtmlAttribute("hx-swap", "outerHTML");
$form->setHtmlAttribute("hx-select", "#" . $form->getID());
```

If you need custom attributes simply add them as needed or replace the existing ones:

```php
$form->setHtmlAttribute("hx-post", false);
```

Pro-Tipp: You can make RockForms preserve the form inputs so that you can submit the same form multiple times, which can be very handy while working on the `processInput()` method of your form!

```php
// in site/config.php
$config->rockformsPreserveSuccess = true;
```

## Adding Loading Animations

When using HTMX RockForms will add a loading overlay to your page after the form was submitted. You can use any loader like the ones at [cssloaders.github.io](https://cssloaders.github.io/) or build your own. See the module config page for options!

You can try a live preview:

[rockforms=Quickstart]

## Custom Error Message

By default RockForms will show a JS alert if the HTMX swap fails. This alert will show an english error message, but you can customize that to show whatever you want (eg a simple alert with different text or even a UIkit modal or such).

```js
// put that in your main js file
// the delay is set to 500ms in this example
// the default alert shows up after 1000ms, so make sure yours fires earlier
document.addEventListener("htmx:beforeSwap", function (e) {
  let selector = "#" + e.target.id;
  setTimeout(() => {
    if (document.querySelector(selector)) return;

    // add rf-custom-alert class to body
    // this tells RockForms to not show the default alert
    document.body.classList.add("rf-custom-alert");

    // show the alert message
    alert("This is my custom HTMX alert!");
  }, 500);
});
```

## Tracking Form Submissions

With HTMX, users aren't redirected to a new URL, making it tricky to track form submissions using a specific URL in analytics tools like Google Analytics or Matomo.

The easy fix? Just add a small JavaScript snippet to the `successMessage` markup. Once injected into the DOM, this code will automatically report the event to your chosen analytics platform:

```php
public function renderSuccess($values)
{
  return "<h2>Thank you for your message!</h2>
    <script>alert('track form submission')</script>";
}
```

For Matomo Analytics I'm using something like this:

```php
public function renderSuccess($values)
{
  return "<h2>Thank you for your message!</h2>
    <script>
    if(typeof _paq !== 'undefined') {
      _paq.push(['trackEvent', 'Form-Submissions', 'Contact-Form']);
    }
    </script>";
}
```

Note that this snippet will only send the event if the user has already given consent to be tracked. Otherwise the _paq object will not be present in the browser and therefore we don't send the event to Matomo.
