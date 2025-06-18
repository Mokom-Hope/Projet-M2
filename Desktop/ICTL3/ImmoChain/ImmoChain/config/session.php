<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Session Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default session "driver" that will be used on
    | your system. By default, we will use the lightweight native PHP session
    | driver however, you may specify one of the other wonderful drivers
    | available here.
    |
    | Supported: "file", "cookie", "database", "apc",
    |            "memcached", "redis", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    |
    | Here you may specify the number of minutes that you want the session
    | to be allowed to remain idle before it expires. If you want them
    | to immediately expire on the browser closing, set that option.
    |
    */

    'lifetime' => env('SESSION_LIFETIME', 120),

    'expire_on_close' => false,

    /*
    |--------------------------------------------------------------------------
    | Session Encryption
    |--------------------------------------------------------------------------
    |
    | This option allows you to easily specify that all of your session data
    | should be encrypted before it is stored. All encryption will be
    | performed using the encryption configurations in app.php.
    |
    */

    'encrypt' => false,

    /*
    |--------------------------------------------------------------------------
    | Session File Location
    |--------------------------------------------------------------------------
    |
    | When using the "file" session driver, we need a location where sessions
    | may be stored. A default has been created for you but feel free to
    | change this location.
    |
    */

    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Session Database Connection
    |--------------------------------------------------------------------------
    |
    | When using the "database" session driver, you should configure the
    | connection that should be used to store your session records. This
    | connection should be defined in the database configuration file.
    |
    */

    'connection' => env('SESSION_CONNECTION', null),

    /*
    |--------------------------------------------------------------------------
    | Session Database Table
    |--------------------------------------------------------------------------
    |
    | When using the "database" session driver, you may specify the table we
    | should use to store the sessions. Of course, a sensible default is
    | provided for you; however, you are free to change this as needed.
    |
    */

    'table' => 'sessions',

    /*
    |--------------------------------------------------------------------------
    | Session Store
    |--------------------------------------------------------------------------
    |
    | This option allows you to define a custom session store that is used
    | by the "database", "redis", and "memcached" session drivers. It will
    | be used to manage the sessions in a more granular way.
    |
    */

    'store' => env('SESSION_STORE', null),

    /*
    |--------------------------------------------------------------------------
    | Session Lottery Chance
    |--------------------------------------------------------------------------
    |
    | Some session drivers must have a garbage collection process run on
    | them periodically to remove old records from storage. These options
    | control the probability that the "garbage collection" will run.
    |
    */

    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Name
    |--------------------------------------------------------------------------
    |
    | Here you may change the name of the cookie used to identify a session
    | instance by your application. If you leave this empty, the name
    | will be generated for you based on your application name.
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        str_replace(' ', '_', strtolower(env('APP_NAME', 'laravel'))).'_session'
    ),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Path
    |--------------------------------------------------------------------------
    |
    | The session cookie path determines the path for which the cookie will
    | be regarded as available. Typically, this will be '/', but you
    | may configure this as needed.
    |
    */

    'path' => '/',

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Domain
    |--------------------------------------------------------------------------
    |
    | Here you may change the domain of the cookie used to identify a session
    | in your application. This will determine which domains the cookie is
    | available to in your application. A sensible default is provided.
    |
    */

    'domain' => env('SESSION_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | HTTPS Only Cookies
    |--------------------------------------------------------------------------
    |
    | By setting this option to true, session cookies will only be sent back
    | to the server if the browser has a HTTPS connection. This will
    | keep the cookie from being sent to you if it can not be done safely.
    |
    */

    'secure' => env('SESSION_SECURE_COOKIE', false),

    /*
    |--------------------------------------------------------------------------
    | HTTP Only Cookies
    |--------------------------------------------------------------------------
    |
    | When this option is enabled, the session cookie will be marked as
    | accessible only through the HTTP protocol and not JavaScript. This
    | reduces the risk of XSS attacks on your application.
    |
    */

    'http_only' => true,

    /*
    |--------------------------------------------------------------------------
    | Same-Site Cookies
    |--------------------------------------------------------------------------
    |
    | This option determines how your cookies behave when cross-site tracking
    | is enabled. It will also have an impact on other cookies as well,
    | so be sure this is something you want to enable.
    |
    | Supported: 'lax', 'strict', 'none'
    |
    */

    'same_site' => 'lax',

];

