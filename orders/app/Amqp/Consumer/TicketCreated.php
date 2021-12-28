<?php

declare(strict_types=1);

namespace Orders\Amqp\Consumer;

use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use Orders\Model\Ticket;
use PhpAmqpLib\Message\AMQPMessage;

#[Consumer(exchange: 'tickets', routingKey: 'ticket-created', queue: 'orders-ticket-created', name: "TicketCreated", nums: 1)]
class TicketCreated extends ConsumerMessage
{
    public function consumeMessage($data, AMQPMessage $message): string
    {
        Ticket::create($data);

        return Result::ACK;
    }
}
