# GremoCaptchaFormBundle
[![Latest stable](https://img.shields.io/packagist/v/gremo/captcha-form-bundle.svg?style=flat-square)](https://packagist.org/packages/gremo/captcha-form-bundle) [![Downloads total](https://img.shields.io/packagist/dt/gremo/captcha-form-bundle.svg?style=flat-square)](https://packagist.org/packages/gremo/captcha-form-bundle)

Symfony bundle that provides CAPTCHA form field to solve challenge-response tests. Supports multiple adapters as well as 
custom ones. Built-in adapter for:

- [Google reCAPTCHA](https://www.google.com/recaptcha)
- [Gregwar/Captcha](https://github.com/Gregwar/Captcha)
- [Honeypot captcha technique](http://haacked.com/archive/2007/09/11/honeypot-captcha.aspx/)

New contributors are welcome!

## Installation
Add the bundle in your `composer.json` file:

```js
{
    "require": {
        "gremo/captcha-form-bundle": "~1.0"
    }
}
```
Then enable the bundle in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Gremo\CaptchaFormBundle\GremoCaptchaFormBundle(),
        // ...
    );
}
```

## Configuration
```yml
# GremoCaptchaFormBundle Configuration
gremo_captcha_form:
    # Default template, change only if you know what your are doing
    template: 'GremoCaptchaFormBundle::default.html.twig'
    
    # Default adapter (default to the first adapter)
    default_adapter: ~
 
    # Adapters (one or more) configuration
    adapters:
        # Adapter key and its options
        adapter_key1: []
        
        # ... and another adapter
        adapter_key2: []
```

In order to use the CAPTCHA form you need to configure at least one adapter (see "Adapters" section).

## Usage
You can use the generic form type instead of the form provided by each adapter. This is more maintainable as you depends only on one form type.

The generic type use the default adapter and options provided in the configuration. An example usage:

```php
// For Symfony >= 2.7 and PHP >= 5.5 use the class name resolution via ::class
use Gremo\CaptchaFormBundle\Form\Type\CaptchaType;

$builder->add('captcha', CaptchaType::class, [
    // Pass custom options to override defaults from configuration
]);

// For Symfony >= 2.7 and PHP < 5.5 use the fully-qualified class name as string
$builder->add('captcha', 'Gremo\CaptchaFormBundle\Form\Type\CaptchaType', [
    // Pass custom options to override defaults from configuration
]);

// For Symfony < 2.7
$builder->add('captcha', 'gremo_captcha', [
    // Pass custom options to override defaults from configuration
]);

```

## Adapters
At least one adapter must be configured.

### Google reCAPTCHA adapter
**Adapter key**: `recaptcha` **Form Type**: `Gremo\CaptchaFormBundle\Form\Type\RecaptchaType`

Add the `google/recaptcha` library in your `composer.json` file:

```js
{
    "require": {
        "google/recaptcha": "~1.1"
    }
}
```

The run `composer update`. Available configuration ([options explanation](https://developers.google.com/recaptcha/docs/display#render_param)):

```yml
# ...
adapters:
    # ...
    recaptcha:
        # Mandatory options
        key:              ~ # string
        secret:           ~ # string        

		# Not mandatory options
        theme:            ~ # string
        type:             ~ # string
        size:             ~ # string
        tabindex:         ~ # integer
		callback:         ~ # string
		expired_callback: ~ # string
```

> **Tip**: add the `hl` parameter to the script in order to localize the CAPTCHA, i.e. in Twig `<script src="https://www.google.com/recaptcha/api.js?hl={{ app.request.locale }}" async defer></script>`

Example usage:

```php
// For Symfony >= 2.7 and PHP >= 5.5 use the class name resolution via ::class
use Gremo\CaptchaFormBundle\Form\Type\RecaptchaType;

$builder->add('captcha', RecaptchaType::class, [
    // Pass custom options to override defaults from configuration
]);

// For Symfony >= 2.7 and PHP < 5.5 use the fully-qualified class name as string
$builder->add('captcha', 'Gremo\CaptchaFormBundle\Form\Type\RecaptchaType', [
    // Pass custom options to override defaults from configuration
]);

// For Symfony < 2.7
$builder->add('captcha', 'gremo_captcha_recaptcha', [
    // Pass custom options to override defaults from configuration
]);
```

### Gregwar captcha adapter
**Adapter key**: `gregwar_captcha` **Form Type**: `Gremo\CaptchaFormBundle\Form\Type\GregwarCaptchaType`

Add the `gregwar/recaptcha` library in your `composer.json` file:

```js
{
    "require": {
        "gregwar/captcha": "~1.1"
    }
}
```

The run `composer update`. Available configuration ([options explanation](https://github.com/Gregwar/Captcha)):

```yml
# ...
adapters:
    # ...
    gregwar_captcha:
		# Not mandatory options
        storage_key:        _gregwar_captcha
        width:              ~ # integer
        height:             ~ # integer
        quality:            ~ # integer
        font:               ~ # string
		distorsion:         ~ # boolean
		interpolation:      ~ # boolean
		ignore_all_effects: ~ # boolean
		orc:                ~ # boolean
```

Example usage:

```php
// For Symfony >= 2.7 and PHP >= 5.5 use the class name resolution via ::class
use Gremo\CaptchaFormBundle\Form\Type\GregwarCaptchaType;

$builder->add('captcha', GregwarCaptchaType::class, [
    // Pass custom options to override defaults from configuration
]);

// For Symfony >= 2.7 and PHP < 5.5 use the fully-qualified class name as string
$builder->add('captcha', 'Gremo\CaptchaFormBundle\Form\Type\GregwarCaptchaType', [
    // Pass custom options to override defaults from configuration
]);

// For Symfony < 2.7
$builder->add('captcha', 'gremo_captcha_gregwar', [
    // Pass custom options to override defaults from configuration
]);
```

### Honeypot adapter
**Adapter key**: `honeypot` **Form Type**: `Gremo\CaptchaFormBundle\Form\Type\HoneypotType`

Available configuration:

```yml
# ...
adapters:
    # ...
    honeypot:
		# Mandatory options
        type: ~ # string, "text" or "hidden" or their FQCN (Symfony >= 2.7)

```

Example usage:

```php
// For Symfony >= 2.7 and PHP >= 5.5 use the class name resolution via ::class
use Gremo\CaptchaFormBundle\Form\Type\HoneypotType;

$builder->add('captcha', HoneypotType::class, [
    // Pass custom options to override defaults from configuration
]);

// For Symfony >= 2.7 and PHP < 5.5 use the fully-qualified class name as string
$builder->add('captcha', 'Gremo\CaptchaFormBundle\Form\Type\HoneypotType', [
    // Pass custom options to override defaults from configuration
]);

// For Symfony < 2.7
$builder->add('captcha', 'gremo_captcha_honeypot', [
    // Pass custom options to override defaults from configuration
]);
```
