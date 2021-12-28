<?php

declare(strict_types=1);

use Hyperf\Session\Handler;

return [
    'handler' => Handler\RedisHandler::class,
    'options' => [
        'connection' => 'default',
        'gc_maxlifetime' => 1200,
        'session_name' => 'AUTH_SESSION_ID',
        'domain' => null,
        'cookie_lifetime' => 5 * 60 * 60,
    ],
];
