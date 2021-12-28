<?php

declare(strict_types=1);

namespace Expiration\Amqp\Producer;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;

#[Producer(exchange: 'expiration', routingKey: 'expiration-complete')]
class ExpirationComplete extends ProducerMessage
{
    public function __construct(int $orderId)
    {
        $this->payload = ['id' => $orderId];
    }
}
