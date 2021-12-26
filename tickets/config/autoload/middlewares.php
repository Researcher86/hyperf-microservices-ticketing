<?php

declare(strict_types=1);

use Tickets\Middleware\AuthMiddleware;
use Tickets\Middleware\CorsMiddleware;
use Hyperf\Session\Middleware\SessionMiddleware;
use Hyperf\Validation\Middleware\ValidationMiddleware;

return [
    'http' => [
        CorsMiddleware::class,
//        ValidationMiddleware::class,
//        SessionMiddleware::class,
    ],
];
