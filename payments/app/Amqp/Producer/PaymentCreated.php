<?php

declare(strict_types=1);

namespace Payments\Amqp\Producer;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;

#[Producer(exchange: 'payments', routingKey: 'payment-created')]
class PaymentCreated extends ProducerMessage
{
    public function __construct($data)
    {
        $this->payload = $data;
    }
}
