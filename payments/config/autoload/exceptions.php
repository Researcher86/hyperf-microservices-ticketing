<?php

declare(strict_types=1);

use Payments\Exception\Handler\AppExceptionHandler;
use Payments\Exception\Handler\DefaultExceptionHandler;
use Payments\Exception\Handler\HttpExceptionHandler;
use Payments\Exception\Handler\Validation\ValidationExceptionHandler;

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
