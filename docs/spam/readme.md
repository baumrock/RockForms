# Spam Protection

RockForms makes it easy to protect your forms from spambots.

## Submit Delay

RockForms introduces an innovative spam protection method that enhances user experience while effectively keeping bots at bay. This method involves a hidden text field within the form, dynamically populated through JavaScript as the user navigates the site. The field's value increments every second, tracking the user's visit duration.

This technique is based on the simple principle that bots submit forms almost immediately upon page load, unlike human users who take time. Thus, the hidden field's value serves as an excellent indicator of genuine user activity, distinguishing legitimate submissions from potential spam without intrusive verification methods.

Pro-Tip: Combine this technique with the "WireRequestBlocker" Pro Module for enhanced protection, blocking bot IPs from your site and conserving server resources for genuine submissions.

## Honeypot Fields

<img src=honey.jpg class=blur>

Honeypot fields are a clever and non-intrusive way to protect your forms from automated spam submissions. They work by adding fields to your form that are invisible to human users but are likely to be filled out by bots. When a form submission includes data in these honeypot fields, RockForms can automatically identify it as spam and block the submission.

### How to Implement Honeypot Fields

RockForms simplifies the process of adding honeypot fields to your forms. Here's how you can use them:

1. **Enable Honeypot Fields**: To enable honeypot fields, you don't need to do anything manually in your form's code. RockForms automatically adds them based on the configuration you set in the module's settings.

2. **Configure Honeypot Fields**: You can specify the names of the honeypot fields in the RockForms module settings. Navigate to the module's configuration page and find the 'Honeypot Fields' setting. Enter one field name per line. Choose names that are likely to attract bot submissions, such as "email", "url", or "comment" but make sure it's a name that you don't need for actual data. For example you could use "email" for the honeypot and use "mail" as real data field.

3. **Customize CSS**: While honeypot fields are invisible to humans, they are hidden using CSS. Ensure that your site's CSS includes the necessary styles to hide these fields. Typically, you would add a rule like `.rf-hny { display: none; }` to your stylesheet.

4. **Monitor and Adjust**: After implementing honeypot fields, monitor your form submissions for a while. If you notice that spam is still getting through, consider changing the names of your honeypot fields or adding more fields.

## External Anti-Spam Providers

RockForms also supports integration with external anti-spam services like CleanTalk to provide an additional layer of protection against spam. These services can help identify and block spam submissions more effectively by analyzing various factors such as the sender's behavior, reputation, and the content of the submission.

Integration is straightforward! Simply add a custom check to your Form's `processInput` method and if the service detects spam just add an error to the form:

```php
  public function processInput()
  {
    // get submitted form values
    $values = $this->getValues();

    // your code to call the external service
    $isSpam = $yourService->getSpamResult($values);

    // add an error to the form and early exit
    if($isSpam) {
      $this->addError("We don't like spam, sorry!");
      return;
    }

    // save form to DB
    // send mail notification
  }
```

## CSRF

Cross-Site Request Forgery (CSRF) is a common attack vector on the web, where unauthorized commands are transmitted from a user that the web application trusts. Typically, protection against CSRF involves generating a unique token for each user session and validating this token with each request to ensure its authenticity. However, integrating CSRF protection in environments that utilize caching mechanisms like ProCache can be challenging.

ProCache is designed to serve static copies of your web pages for enhanced performance. Since CSRF tokens are dynamic and unique for each session, they conflict with the static nature of cached pages. This means that traditional CSRF protection methods that rely on server-generated tokens cannot be directly used with ProCache.

As a workaround to this limitation, RockForms employs alternative anti-spam techniques that do not require dynamic server requests. One such technique is the use of honeypot fields, which are already discussed in this document. Honeypot fields are effective against automated spam without needing to alter the static cache.

In addition to honeypots, we are exploring the implementation of a JavaScript-based solution that measures the time taken to fill and submit a form. Bots typically submit forms much faster than a human would, so by setting a minimum time threshold for form submission, we can further mitigate spam without the need for server-side token generation or validation. This approach will complement our existing methods and provide robust spam protection that is fully compatible with ProCache.

By leveraging these techniques, RockForms aims to provide effective spam protection while maintaining compatibility with caching solutions, ensuring that your site's performance is not compromised.
