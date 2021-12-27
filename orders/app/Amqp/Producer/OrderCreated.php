<?php

declare(strict_types=1);

namespace Orders\Amqp\Producer;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;

#[Producer(exchange: 'orders', routingKey: 'order-created')]
class OrderCreated extends ProducerMessage
{
    public function __construct($data)
    {
        $this->payload = $data;
    }
}
