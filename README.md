# MandrillForm

MandrillForm for Craft is very similar to Pixel & Tonic's ContactForm plugin, but uses Mandrill for sending emails instead of your native mail server (if you have one).

## How to install and use:

1. Place the mandrillform folder in your craft/plugins folder.
2. Go to Settings > Plugins from your Craft control panel and enable the MandrillForm plugin.
3. Click on "MandrillForm" to go to the plugin's settings page.
4. Enter the email address you would like the contact requests to be sent to.
5. Enter your Mandrill API key.
6. Enter the subject to appear in the emails you receive.

## Sample Form


```
{% macro errorList(errors) %}
    {% if errors %}
        <ul class="errors">
            {% for error in errors %}
                <li>{{ error }}</li>
            {% endfor %}
        </ul>
    {% endif %}
{% endmacro %}

<form method="post" action="" accept-charset="UTF-8">
    <input type="hidden" name="action" value="mandrillForm/sendMessage">
    <input type="hidden" name="successRedirectUrl" value="success">

    <h3><label for="fromEmail">From Email</label></h3>
    <input id="fromEmail" type="text" name="fromEmail" value="{% if message is defined %}{{ message.fromEmail }}{% endif %}">

    {% if message is defined %}
        {{ _self.errorList(message.getErrors('fromEmail')) }}
    {% endif %}

    <h3><label for="fromName">From Name</label></h3>
    <input id="fromName" type="text" name="fromName" value="{% if message is defined %}{{ message.fromName }}{% endif %}">

    {% if message is defined %}
        {{ _self.errorList(message.getErrors('fromName')) }}
    {% endif %}

    <h3><label for="subject">Subject</label></h3>
    <input id="subject" type="text" name="subject" value="{% if message is defined %}{{ message.subject }}{% endif %}">

    {% if message is defined %}
        {{ _self.errorList(message.getErrors('subject')) }}
    {% endif %}

    <h3><label for="message">Message</label></h3>
    <textarea rows="10" cols="40" id="message" name="message">{% if message is defined %}{{ message.message }}{% endif %}</textarea>

    {% if message is defined %}
        {{ _self.errorList(message.getErrors('message')) }}
    {% endif %}

    <input type="submit" value="Submit">
</form>
```

## Roadmap

Right now I've built this plugin very quickly to meet the very specific needs of another project I'm working on.  So I've done the bare minimum to get this working, but the more I interact with Mandrill, the more I like the service so I definitely plan on extending the functionality.

- [ ] Robust error handling
- [ ] Tags
- [ ] Better examples (especially AJAX form submissions)

## Special Notes
This plugin currently includes v1.0.55 of the Official Mandrill API Client for PHP.  The latest wrapper can be found on [this packagist repo](https://packagist.org/packages/mandrill/mandrill).
