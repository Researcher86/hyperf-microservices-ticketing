<?php

declare(strict_types=1);

namespace Payments\Amqp\Consumer;

use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Producer;
use Hyperf\Amqp\Result;
use PhpAmqpLib\Message\AMQPMessage;
use Payments\Exception\BusinessException;
use Payments\Model\Order;

#[Consumer(exchange: 'orders', routingKey: 'order-cancelled', queue: 'payments-order-cancelled', name: "OrderCancelled", nums: 1)]
class OrderCancelled extends ConsumerMessage
{
    private Producer $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function consumeMessage($data, AMQPMessage $message): string
    {
        $version = $data['version'] - 1;
        $order = Order::query()
            ->where('id', '=', $data['id'])
            ->where('version', '=', $version)
            ->first();

        if (!$order) {
            throw new BusinessException(0, sprintf('Order by id [%d] and version [%d] not found', $data['id'], $version));
        }

        $order->update(['status' => Order::STATUS_CANCELLED]);

        return Result::ACK;
    }
}
