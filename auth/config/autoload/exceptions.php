<?php

declare(strict_types=1);

use Auth\Exception\Handler\AppExceptionHandler;
use Auth\Exception\Handler\DefaultExceptionHandler;
use Auth\Exception\Handler\HttpExceptionHandler;
use Auth\Exception\Handler\Validation\ValidationExceptionHandler;

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
