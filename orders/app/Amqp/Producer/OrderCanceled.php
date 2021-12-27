<?php

declare(strict_types=1);

namespace Orders\Amqp\Producer;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;

#[Producer(exchange: 'orders', routingKey: 'order-canceled')]
class OrderCanceled extends ProducerMessage
{
    public function __construct(int $orderId, int $ticketId)
    {
        $this->payload = ['id' => $orderId, 'ticket_id' => $ticketId];
    }
}
