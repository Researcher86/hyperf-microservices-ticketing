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
                'namespace' => 'Auth\\Amqp\\Consumer',
            ],
            'producer' => [
                'namespace' => 'Auth\\Amqp\\Producer',
            ],
        ],
        'aspect' => [
            'namespace' => 'Auth\\Aspect',
        ],
        'command' => [
            'namespace' => 'Auth\\Command',
        ],
        'controller' => [
            'namespace' => 'Auth\\Controller',
        ],
        'job' => [
            'namespace' => 'Auth\\Job',
        ],
        'listener' => [
            'namespace' => 'Auth\\Listener',
        ],
        'middleware' => [
            'namespace' => 'Auth\\Middleware',
        ],
        'Process' => [
            'namespace' => 'Auth\\Processes',
        ],
    ],
];
