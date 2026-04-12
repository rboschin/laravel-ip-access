<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IP Access Mode
    |--------------------------------------------------------------------------
    |
    | This value determines the mode of IP access control.
    | Supported: "whitelist", "blacklist"
    |
    | - whitelist: Only IPs in the whitelist can access the system
    | - blacklist: All IPs can access except those in the blacklist
    |
    */

    'mode' => env('IP_ACCESS_MODE', 'whitelist'),

    /*
    |--------------------------------------------------------------------------
    | Whitelist Source
    |--------------------------------------------------------------------------
    |
    | Define where to get the whitelist from. Options:
    | - ".env": Read from IP_WHITELIST environment variable
    | - "IpAccessWhite": Read from the IpAccessWhite database model
    |
    | Example in .env: IP_ACCESS_WHITELIST_SOURCE=.env
    | Example in .env: IP_ACCESS_WHITELIST_SOURCE=IpAccessWhite
    |
    */

    'whitelist_source' => env('IP_ACCESS_WHITELIST_SOURCE', '.env'),

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist
    |--------------------------------------------------------------------------
    |
    | List of IP addresses that are allowed to access the system when
    | mode is set to "whitelist" and whitelist_source is ".env".
    | Separate multiple IPs with commas.
    |
    | Example in .env: IP_WHITELIST=127.0.0.1,192.168.1.100,10.0.0.*
    |
    */

    'whitelist' => array_filter(array_map('trim', explode(',', env('IP_WHITELIST', '')))),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Source
    |--------------------------------------------------------------------------
    |
    | Define where to get the blacklist from. Options:
    | - ".env": Read from IP_BLACKLIST environment variable
    | - "IpAccessBlack": Read from the IpAccessBlack database model
    |
    | Example in .env: IP_ACCESS_BLACKLIST_SOURCE=.env
    | Example in .env: IP_ACCESS_BLACKLIST_SOURCE=IpAccessBlack
    |
    */

    'blacklist_source' => env('IP_ACCESS_BLACKLIST_SOURCE', '.env'),

    /*
    |--------------------------------------------------------------------------
    | IP Blacklist
    |--------------------------------------------------------------------------
    |
    | List of IP addresses that are blocked from accessing the system when
    | mode is set to "blacklist" and blacklist_source is ".env".
    | Separate multiple IPs with commas.
    |
    | Example in .env: IP_BLACKLIST=123.45.67.89,98.76.54.32
    |
    */

    'blacklist' => array_filter(array_map('trim', explode(',', env('IP_BLACKLIST', '')))),

    /*
    |--------------------------------------------------------------------------
    | Trust Proxies
    |--------------------------------------------------------------------------
    |
    | When set to true, the middleware will check the X-Forwarded-For header
    | to get the real client IP when behind a proxy or load balancer.
    |
    */

    'trust_proxies' => env('IP_ACCESS_TRUST_PROXIES', true),

    /*
    |--------------------------------------------------------------------------
    | Forbidden Message
    |--------------------------------------------------------------------------
    |
    | The message to display when access is denied.
    |
    */

    'forbidden_message' => env('IP_ACCESS_FORBIDDEN_MESSAGE', 'Access denied.') . ': ' . $_SERVER['REMOTE_ADDR'],

];
