<?php

declare(strict_types=1);

namespace Orders\Amqp\Consumer;

use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use Orders\Exception\BusinessException;
use Orders\Model\Ticket;
use PhpAmqpLib\Message\AMQPMessage;

#[Consumer(exchange: 'tickets', routingKey: 'ticket-updated', queue: 'orders-ticket-updated', name: "TicketUpdated", nums: 1)]
class TicketUpdated extends ConsumerMessage
{
    public function consumeMessage($data, AMQPMessage $message): string
    {
        $version = $data['version'] - 1;
        $ticket = Ticket::query()
            ->where('id', '=', $data['id'])
            ->where('version', '=', $version)
            ->first();

        if (!$ticket) {
            throw new BusinessException(0, sprintf('Ticket by id [%d] and version [%d] not found', $data['id'], $version));
        }

        $ticket->update($data);

        return Result::ACK;
    }
}
