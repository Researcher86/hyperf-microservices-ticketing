<?php

declare(strict_types=1);

return [
    'generator' => [
        'amqp' => [
            'consumer' => [
                'namespace' => 'Expiration\\Amqp\\Consumer',
            ],
            'producer' => [
                'namespace' => 'Expiration\\Amqp\\Producer',
            ],
        ],
        'aspect' => [
            'namespace' => 'Expiration\\Aspect',
        ],
        'command' => [
            'namespace' => 'Expiration\\Command',
        ],
        'controller' => [
            'namespace' => 'Expiration\\Controller',
        ],
        'job' => [
            'namespace' => 'Expiration\\Job',
        ],
        'listener' => [
            'namespace' => 'Expiration\\Listener',
        ],
        'middleware' => [
            'namespace' => 'Expiration\\Middleware',
        ],
        'Process' => [
            'namespace' => 'Expiration\\Processes',
        ],
    ],
];
