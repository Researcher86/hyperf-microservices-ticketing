<?php

declare(strict_types=1);

use Expiration\Exception\Handler\AppExceptionHandler;
use Expiration\Exception\Handler\DefaultExceptionHandler;
use Expiration\Exception\Handler\HttpExceptionHandler;
use Expiration\Exception\Handler\Validation\ValidationExceptionHandler;

return [
    'handler' => [
        'http' => [
            ValidationExceptionHandler::class,
            HttpExceptionHandler::class,
            AppExceptionHandler::class,
            DefaultExceptionHandler::class,
        ],
    ],
];
