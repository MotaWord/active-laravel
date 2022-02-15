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
        | Active JS URL
        |--------------------------------------------------------------------------
        |
        | This is the base URL for front facing Active experience. This is typically used if Serve is disabled.
        |
        */
        'active_js_url' => env('MOTAWORD_ACTIVE_JS_URL', 'https://active-js.motaword.com/index.js'),

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
