<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    'generator' => [
        'amqp' => [
            'consumer' => [
                'namespace' => 'Tickets\\Amqp\\Consumer',
            ],
            'producer' => [
                'namespace' => 'Tickets\\Amqp\\Producer',
            ],
        ],
        'aspect' => [
            'namespace' => 'Tickets\\Aspect',
        ],
        'command' => [
            'namespace' => 'Tickets\\Command',
        ],
        'controller' => [
            'namespace' => 'Tickets\\Controller',
        ],
        'job' => [
            'namespace' => 'Tickets\\Job',
        ],
        'listener' => [
            'namespace' => 'Tickets\\Listener',
        ],
        'middleware' => [
            'namespace' => 'Tickets\\Middleware',
        ],
        'Process' => [
            'namespace' => 'Tickets\\Processes',
        ],
    ],
];
