[![Latest Version on Packagist](https://img.shields.io/packagist/v/codebar-ag/laravel-prerender.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-prerender)
[![Total Downloads](https://img.shields.io/packagist/dt/codebar-ag/laravel-prerender.svg?style=flat-square)](https://packagist.org/packages/codebar-ag/laravel-prerender)
[![run-tests](https://github.com/codebar-ag/laravel-prerender/actions/workflows/run-tests.yml/badge.svg)](https://github.com/codebar-ag/laravel-prerender/actions/workflows/run-tests.yml)
[![Check & fix styling](https://github.com/codebar-ag/laravel-prerender/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/codebar-ag/laravel-prerender/actions/workflows/php-cs-fixer.yml)

This package was developed to give you a quick start to integrate with the
MotaWord Active service to localize your Laravel application.

## 🙇 Credits

This package is a clone from [codebar-ag/laravel-prerender](https://github.com/codebar-ag/laravel-prerender).

## 💡 What is MotaWord Active?


## 🛠 Requirements

- PHP: `^7.2 to ^8.2`
- Laravel: `^6 to ^10`
- MotaWord Active access

## ⚙️ Installation

You can install the package via composer:

```shell
composer require motaword/active
```

Then, add the following to your `.env` file:

```dotenv
MOTAWORD_ACTIVE_TOKEN=active token from your MotaWord dashboard
MOTAWORD_ACTIVE_PROJECT_ID=project ID from your MotaWord dashboard
MOTAWORD_ACTIVE_WIDGET_ID=widget ID from your MotaWord dashboard 
```

That's it. Every GET-Request from a crawler will be forwarded to MotaWord's Active Serve.

## ✋ Disable Active Serve (SEO + CDN)

You can disable Active Serve service by adding the following to your `.env` file:

```dotenv
MOTAWORD_ACTIVE_SERVE_ENABLE=false
```

This may be useful for your local development environment.

## ✏️ How it works

1. The middleware checks to make sure we should show a prerendered page
	1. The middleware checks if the request is from a crawler (agent string or `_escaped_fragment_`)
	2. The middleware checks to make sure we aren't requesting a resource (js, css, etc...)
	3. (optional) The middleware checks to make sure the url is in the whitelist
	4. (optional) The middleware checks to make sure the url isn't in the blacklist
2. The middleware makes a `GET` request to Active Serve for the page's HTML
3. Return the HTML to the crawler

## 🔧 Configuration file

You can publish the config file with:

```shell
php artisan vendor:publish --provider="MotaWord\Active\MotaWordActiveServiceProvider"
```

Afterwards you can customize the Whitelist/Blacklist on your own.

This is the contents of the published config file:

```php
<?php

return [
    'active' => [
        /*
        |--------------------------------------------------------------------------
        | MotaWord Active Project Token
        |--------------------------------------------------------------------------
        |
        | Set your MotaWord Active token here. It will be sent via the X-MotaWord-Token header.
        |
        */
        'token' => env('MOTAWORD_ACTIVE_TOKEN'),
        /*
        |--------------------------------------------------------------------------
        | MotaWord Active Project ID
        |--------------------------------------------------------------------------
        |
        | Set your MotaWord Active project ID here. You can find this ID on your MotaWord dashboard, under Active > Configuration.
        |
        */
        'project_id' => env('MOTAWORD_ACTIVE_PROJECT_ID'),
        /*
        |--------------------------------------------------------------------------
        | MotaWord Active - Widget ID
        |--------------------------------------------------------------------------
        |
        | Set your MotaWord Active widget ID here.  You can find this ID on your MotaWord dashboard, under Active > Configuration.
        |
        */
        'widget_id' => env('MOTAWORD_ACTIVE_WIDGET_ID'),

        /*
        |--------------------------------------------------------------------------
        | Enable MotaWord Active
        |--------------------------------------------------------------------------
        |
        | Set this field to false to fully disable the MotaWord Active service.
        |
        */
        'serve_enable' => env('MOTAWORD_ACTIVE_SERVE_ENABLE', true),

        /*
        |--------------------------------------------------------------------------
        | Serve URL
        |--------------------------------------------------------------------------
        |
        | This is the base URL for our localization-specific CDN service, Active Serve.
        |
        */
        'serve_url' => env('MOTAWORD_ACTIVE_SERVE_URL', 'https://serve.motaword.com'),

        /*
        |--------------------------------------------------------------------------
        | Return soft HTTP status codes
        |--------------------------------------------------------------------------
        |
        | By default MotaWord Active returns soft HTTP codes. If you would like it to
        | return the real ones in case of Redirection (3xx) or status Not Found (404),
        | set this parameter to false.
        | Keep in mind that returning real HTTP codes requires appropriate meta tags
        | to be set. For more details, see github.com/motaword/active-laravel#httpheaders
        |
        */
        'soft_http_codes' => env('MOTAWORD_ACTIVE_SOFT_HTTP_STATUS_CODES', true),

        /*
        |--------------------------------------------------------------------------
        | MotaWord Active Whitelist
        |--------------------------------------------------------------------------
        |
        | Whitelist paths or patterns. You can use asterix syntax, or regular
        | expressions (without start and end markers). If a whitelist is supplied,
        | only url's containing a whitelist path will be prerendered. An empty
        | array means that all URIs will pass this filter. Note that this is the
        | full request URI, so including starting slash and query parameter string.
        | See github.com/JeroenNoten/Laravel-Prerender for an example.
        |
        */
        'whitelist' => [],

        /*
        |--------------------------------------------------------------------------
        | MotaWord Active Blacklist
        |--------------------------------------------------------------------------
        |
        | Blacklist paths to exclude. You can use asterix syntax, or regular
        | expressions (without start and end markers). If a blacklist is supplied,
        | all url's will be prerendered except ones containing a blacklist path.
        | By default, a set of asset extentions are included (this is actually only
        | necessary when you dynamically provide assets via routes). Note that this
        | is the full request URI, so including starting slash and query parameter
        | string. See github.com/JeroenNoten/Laravel-Prerender for an example.
        |
        */
        'blacklist' => [
            '*.js',
            '*.css',
            '*.xml',
            '*.less',
            '*.png',
            '*.jpg',
            '*.jpeg',
            '*.svg',
            '*.gif',
            '*.pdf',
            '*.doc',
            '*.txt',
            '*.ico',
            '*.rss',
            '*.zip',
            '*.mp3',
            '*.rar',
            '*.exe',
            '*.wmv',
            '*.doc',
            '*.avi',
            '*.ppt',
            '*.mpg',
            '*.mpeg',
            '*.tif',
            '*.wav',
            '*.mov',
            '*.psd',
            '*.ai',
            '*.xls',
            '*.mp4',
            '*.m4a',
            '*.swf',
            '*.dat',
            '*.dmg',
            '*.iso',
            '*.flv',
            '*.m4v',
            '*.torrent',
            '*.eot',
            '*.ttf',
            '*.otf',
            '*.woff',
            '*.woff2'
        ],

        /*
        |--------------------------------------------------------------------------
        | Crawler User Agents
        |--------------------------------------------------------------------------
        |
        | Requests from crawlers that do not support _escaped_fragment_ will
        | nevertheless be served with prerendered pages. You can customize
        | the list of crawlers here.
        |
        */
        'crawler_user_agents' => [
            'googlebot',
            'yahoo',
            'bingbot',
            'yandex',
            'baiduspider',
            'facebookexternalhit',
            'twitterbot',
            'rogerbot',
            'linkedinbot',
            'embedly',
            'bufferbot',
            'quora link preview',
            'showyoubot',
            'outbrain',
            'pinterest',
            'pinterest/0.',
            'developers.google.com/+/web/snippet',
            'www.google.com/webmasters/tools/richsnippets',
            'slackbot',
            'vkShare',
            'W3C_Validator',
            'redditbot',
            'Applebot',
            'WhatsApp',
            'flipboard',
            'tumblr',
            'bitlybot',
            'SkypeUriPreview',
            'nuzzel',
            'Discordbot',
            'Google Page Speed',
            'Qwantify'
        ],
    ],
];
```

### 🤍 Whitelist

Whitelist paths or patterns. You can use asterisk syntax.
If a whitelist is supplied, only urls containing a whitelist path will be sent to Active Serve.
An empty array means that all URIs will pass this filter.
Note that this is the full request URI, so including starting slash and query parameter string.

```php
// motaword.php:
'whitelist' => [
    '/frontend/*' // only Serve pages starting with '/frontend/'
],
```

### 🖤 Blacklist

Blacklist paths to exclude. You can use asterisk syntax.
If a blacklist is supplied, all urls will be sent to Active Serve except ones containing a blacklist path.
By default, a set of asset extensions are included (this is actually only necessary when you dynamically provide assets via routes).
Note that this is the full request URI, so including starting slash and query parameter string.

```php
// motaword.php:
'blacklist' => [
    '/api/*' // do not Serve pages starting with '/api/'
],
```

## 🚧 Local testing

1. Configure MotaWord Active via environment variables:

```dotenv
MOTAWORD_ACTIVE_TOKEN=active token from your MotaWord dashboard
MOTAWORD_ACTIVE_PROJECT_ID=project ID from your MotaWord dashboard
MOTAWORD_ACTIVE_WIDGET_ID=widget ID from your MotaWord dashboard
```

2Test your page like a search engine bot. Make sure to change the URL with your local application URL:

```shell
curl -A Googlebot http://127.0.0.1
```

6. 🎉 That's it — you should see the html output from Active Serve!

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## ✏️ Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## 🧑‍💻 Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## 🎭 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
