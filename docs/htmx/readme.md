# HTMX

<div class="uk-alert uk-alert-warning">Needs <a href=../js/>RockForms.js</a></div>

HTMX transforms your traditional HTML forms into modern forms that use AJAX to submit data. This means you can update your webpage with a success message without reloading the page.

Here's what this approach offers:

- No need for full page reloads, enhancing the user experience.
- It prevents the same form from being submitted more than once.
- The form is only submitted if it passes frontend validation.

RockForms is designed to add as little extra load to your site as possible, ensuring it remains speedy. The HTMX library is loaded only when the user interacts with your form, keeping things efficient.

This is why RockForms will use HTMX for all forms by default. If you are already using HTMX on your site RockFrontend will not load it again.

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

## Adding Loading Animations

When using HTMX RockForms will add a loading overlay to your page after the form was submitted. You can use any loader like the ones at [cssloaders.github.io](https://cssloaders.github.io/) or build your own. See the module config page for options!

You can try a live preview:

[rockforms=Quickstart]

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
