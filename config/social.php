<?php

return [
    'credential_mode' => 'global',

    'post_template' => env('SOCIAL_SHARE_TEMPLATE', ':title — :url'),
    'hashtags' => array_values(array_filter(array_map(
        fn (string $value): string => trim($value),
        explode(',', (string) env('SOCIAL_SHARE_HASHTAGS', 'laravel,php')),
    ))),

    'x' => [
        'enabled' => (bool) env('SOCIAL_X_ENABLED', false),
        'api_url' => env('SOCIAL_X_API_URL', 'https://api.x.com/2/tweets'),
        'bearer_token' => env('SOCIAL_X_BEARER_TOKEN'),
    ],

    'linkedin' => [
        'enabled' => (bool) env('SOCIAL_LINKEDIN_ENABLED', false),
        'api_url' => env('SOCIAL_LINKEDIN_API_URL', 'https://api.linkedin.com/v2/ugcPosts'),
        'access_token' => env('SOCIAL_LINKEDIN_ACCESS_TOKEN'),
        'author_urn' => env('SOCIAL_LINKEDIN_AUTHOR_URN'),
    ],
];
