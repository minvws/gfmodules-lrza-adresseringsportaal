<?php

declare(strict_types=1);

return [
    'timeline' => [
        'endpoint' => env('TIMELINE_ENDPOINT', ''),
        'provider_id' => env('TIMELINE_PROVIDER_ID', ''),
    ],
    'pseudonym' => [
        'endpoint' => env('PSEUDONYM_ENDPOINT', ''),
    ],
];
