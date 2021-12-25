<?php

declare(strict_types=1);

use App\Exception\Handler\AppExceptionHandler;
use App\Exception\Handler\DefaultExceptionHandler;
use App\Exception\Handler\HttpExceptionHandler;
use App\Exception\Handler\Validation\ValidationExceptionHandler;

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
