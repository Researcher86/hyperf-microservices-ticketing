<?php

declare(strict_types=1);

return [
    'generator' => [
        'amqp' => [
            'consumer' => [
                'namespace' => 'Payments\\Amqp\\Consumer',
            ],
            'producer' => [
                'namespace' => 'Payments\\Amqp\\Producer',
            ],
        ],
        'aspect' => [
            'namespace' => 'Payments\\Aspect',
        ],
        'command' => [
            'namespace' => 'Payments\\Command',
        ],
        'controller' => [
            'namespace' => 'Payments\\Controller',
        ],
        'job' => [
            'namespace' => 'Payments\\Job',
        ],
        'listener' => [
            'namespace' => 'Payments\\Listener',
        ],
        'middleware' => [
            'namespace' => 'Payments\\Middleware',
        ],
        'Process' => [
            'namespace' => 'Payments\\Processes',
        ],
    ],
];
