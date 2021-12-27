<?php

declare(strict_types=1);

return [
    'email' => [
        'exists' =>'Email in use'
    ],
    'login' => [
        'unauthorized' => 'Invalid credentials'
    ],
    'token' => [
        'invalid' => 'The token is invalid, preventing further execution',
        'unauthorized' => 'Not authorized',
    ]
];
