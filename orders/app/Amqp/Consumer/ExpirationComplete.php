<?php

declare(strict_types=1);

namespace Orders\Amqp\Consumer;

use Expiration\Exception\BusinessException;
use Hyperf\Amqp\Producer;
use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Orders\Amqp\Producer\OrderCancelled;
use Orders\Model\Order;
use PhpAmqpLib\Message\AMQPMessage;

#[Consumer(exchange: 'expiration', routingKey: 'expiration-complete', queue: 'orders-expiration-complete', name: "ExpirationComplete", nums: 1)]
class ExpirationComplete extends ConsumerMessage
{
    private Producer $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function consumeMessage($data, AMQPMessage $message): string
    {
        $order = Order::find($data['id']);
        if (!$order) {
            throw new BusinessException(0, sprintf('Order by id [%d] not found', $data['id']));
        }

        if ($order->status === Order::STATUS_COMPLETE) {
            return Result::ACK;
        }

        $order->update(['status' => Order::STATUS_CANCELLED]);

        $order->ticket;
        $this->producer->produce(new OrderCancelled($order));

        return Result::ACK;
    }
}
