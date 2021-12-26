<?php

declare(strict_types=1);

namespace Tickets\Amqp\Producer;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;

#[Producer(exchange: 'tickets', routingKey: 'ticket-created')]
class TicketCreated extends ProducerMessage
{
    public function __construct($data)
    {
        $this->payload = $data;
    }
}
