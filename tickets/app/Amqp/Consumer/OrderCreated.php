<?php

declare(strict_types=1);

namespace Tickets\Amqp\Consumer;

use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Producer;
use Hyperf\Amqp\Result;
use PhpAmqpLib\Message\AMQPMessage;
use Tickets\Amqp\Producer\TicketUpdated;
use Tickets\Exception\BusinessException;
use Tickets\Model\Ticket;

#[Consumer(exchange: 'orders', routingKey: 'order-created', queue: 'tickets-order-created', name: "OrderCreated", nums: 1)]
class OrderCreated extends ConsumerMessage
{
    private Producer $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function consumeMessage($data, AMQPMessage $message): string
    {
        $ticket = Ticket::find($data['ticket_id']);
        if (!$ticket) {
            throw new BusinessException(0, sprintf('Ticket id [%d] not found', $data['ticket_id']));
        }

        $ticket->update(['order_id' => $data['id']]);
        $this->producer->produce(new TicketUpdated($ticket));

        return Result::ACK;
    }
}
