# CSRF + ProCache

<div class="uk-alert uk-alert-warning">Needs <a href="../js/">RockForms.js</a></div>

ProCache is great, but it has one problem by design: As soon as you have any dynamic markup on your page the whole static copy gets useless.

This is the case when rendering forms that have any kind of unique markup on it - as it would be the case with CSRF (Cross-Site Request Forgery) tokens.

On the other hand opting out of CSRF protection is not a good idea. CSRF tokens are crucial for the security of your forms. They ensure that the form submissions are genuine and not forged by malicious sites.

The solution in RockForms is to dynamically load CSRF tokens as soon as the user interacts with the form. By implementing this strategy, you can enjoy the benefits of ProCache for static content with blazing fast loading times while still maintaining robust security measures for your forms.

Still not sure what CSRF is or does? See this <a href="https://www.youtube.com/watch?v=80S8h5hEwTY">10 min video about CSRF</a> by Web Dev Simplified.

## Disabling CSRF

If you really don't need a CSRF token for your form, you can disable CSRF for that specific form by adding this to your form's class:

```php
const CSRF = false;
```

Every example file created by RockForms will have that line as a comment, so you don't have to remember it by heart.
