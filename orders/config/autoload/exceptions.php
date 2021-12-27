<?php

declare(strict_types=1);

use Orders\Exception\Handler\AppExceptionHandler;
use Orders\Exception\Handler\DefaultExceptionHandler;
use Orders\Exception\Handler\HttpExceptionHandler;
use Orders\Exception\Handler\Validation\ValidationExceptionHandler;

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
