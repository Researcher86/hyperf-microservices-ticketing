<?php

declare(strict_types=1);

namespace Orders\Amqp\Producer;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;
use Orders\Model\Order;

#[Producer(exchange: 'orders', routingKey: 'order-cancelled')]
class OrderCancelled extends ProducerMessage
{
    public function __construct(Order $order)
    {
        $this->payload = $order;
    }
}
