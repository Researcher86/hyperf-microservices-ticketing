<?php

declare(strict_types=1);

use Tickets\Exception\Handler\AppExceptionHandler;
use Tickets\Exception\Handler\DefaultExceptionHandler;
use Tickets\Exception\Handler\HttpExceptionHandler;
use Tickets\Exception\Handler\Validation\ValidationExceptionHandler;

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
