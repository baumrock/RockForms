# HTMX

HTMX transforms your traditional HTML forms into modern forms that use AJAX to submit data. This means you can update your webpage with a success message without reloading the page.

Here's what this approach offers:

- No need for full page reloads, enhancing the user experience.
- It prevents the same form from being submitted more than once.
- The form is only submitted if it passes frontend validation.

RockForms is designed to add as little extra load to your site as possible, ensuring it remains speedy. The HTMX library is loaded only when the user interacts with your form, keeping things efficient.

This is why RockForms will use HTMX for all forms by default. If you are already using HTMX on your site RockFrontend will not load it again.

## Setup

Seamless integration at its finest! When you're using RockFrontend, everything is configured automatically for you. How cool is that? 😎

### Setup without RockFrontend

If you're not using RockFrontend, the only requirement is to ensure that `RockForms.min.js` is loaded on your site. This is crucial for the proper functioning of RockForms with HTMX. You can manually add the script tag to your site's HTML head section like so:

```php
echo $rockforms->scriptTag();
```

The output will look similar to the following:

```html
<script src="/site/modules/RockForms/assets/RockForms.min.js?sa179u" defer=""></script>
```

Did you notice the cache-busting query string `?sa178u`? RockForms seamlessly integrates this feature, automatically updating it with each upgrade for you.

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

## Adding Cool Loading Animations

Adding a CSS loading animation to your form while it's sending data is simple. Just pick your favorite animation from https://cssloaders.github.io/.

Wondering how to make the animation appear when you submit the form? We'll need to add a bit of JavaScript to the frontend of your site.

For this example, we're going to use UIkit and its <a href=https://getuikit.com/docs/modal>modal component</a> to display an overlay during the data transmission via HTMX.

Let's start by creating the modal markup:

```html
<div id="form-modal" class="uk-modal-full" uk-modal>
  <div class="uk-modal-dialog" style="background: rgba(0,0,0,0.8)">
    <div class="uk-flex uk-flex-center uk-flex-middle" uk-height-viewport>
      <div class="loader"></div>
    </div>
  </div>
</div>
```

To avoid repeatedly submitting the form while working on the loading animation, you can use this JavaScript to automatically display the modal when the page loads:

```html
<script>
UIkit.modal(document.querySelector('#form-modal')).show();
</script>
```

With RockFrontend's livereload feature, you can instantly see your code changes without leaving your IDE!

After setting up your animation, simply add JavaScript to display the modal when submitting and hide it after receiving a response:

```js
// show/hide form submit modal
(() => {
  let modal = UIkit.modal(document.querySelector("#form-modal"));
  document.addEventListener("htmx:beforeRequest", (e) => {
    if (Nette.validateForm(e.target)) modal.show();
  });
  document.addEventListener("htmx:afterSwap", modal.hide);
})();
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
