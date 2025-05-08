<?php

return [
    'site_name' => env('APP_NAME', 'Laravel'),
    'title_append_site_name' => true,
    'title_prepend_site_name' => false,
    'title_separator' => '|',
    'max_title_length' => 60,
    'max_description_length' => 160,

    'defaults' => [
        'robots' => 'index,follow',
        'og_type' => 'website',
        'og_image' => null, // URL to a global fallback OG image
        'twitter_card' => 'summary_large_image',
        'twitter_site' => null, // e.g., '@YourTwitterHandle'
    ],
];
