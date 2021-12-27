<?php

declare(strict_types=1);

return [
    'generator' => [
        'amqp' => [
            'consumer' => [
                'namespace' => 'Orders\\Amqp\\Consumer',
            ],
            'producer' => [
                'namespace' => 'Orders\\Amqp\\Producer',
            ],
        ],
        'aspect' => [
            'namespace' => 'Orders\\Aspect',
        ],
        'command' => [
            'namespace' => 'Orders\\Command',
        ],
        'controller' => [
            'namespace' => 'Orders\\Controller',
        ],
        'job' => [
            'namespace' => 'Orders\\Job',
        ],
        'listener' => [
            'namespace' => 'Orders\\Listener',
        ],
        'middleware' => [
            'namespace' => 'Orders\\Middleware',
        ],
        'Process' => [
            'namespace' => 'Orders\\Processes',
        ],
    ],
];
