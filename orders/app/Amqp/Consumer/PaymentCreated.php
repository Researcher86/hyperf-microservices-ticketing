<?php

declare(strict_types=1);

namespace Orders\Amqp\Consumer;

use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Orders\Exception\BusinessException;
use Orders\Model\Order;
use PhpAmqpLib\Message\AMQPMessage;

#[Consumer(exchange: 'payments', routingKey: 'payment-created', queue: 'orders-payment-created', name: "PaymentCreated", nums: 1)]
class PaymentCreated extends ConsumerMessage
{
    public function consumeMessage($data, AMQPMessage $message): string
    {
        $order = Order::find($data['order_id']);
        if (!$order) {
            throw new BusinessException(0, sprintf('Order by id [%s] not found', $data['order_id']));
        }

        $order->update(['status' => Order::STATUS_COMPLETE]);

        return Result::ACK;
    }
}
