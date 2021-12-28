<?php

declare(strict_types=1);

namespace Expiration\Amqp\Consumer;

use DateTime;
use Expiration\Job\OrderCompleteJob;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use PhpAmqpLib\Message\AMQPMessage;

#[Consumer(exchange: 'orders', routingKey: 'order-created', queue: 'expiration-order-created', name: "OrderCreated", nums: 1)]
class OrderCreated extends ConsumerMessage
{
    private DriverInterface $driver;

    public function __construct(DriverFactory $driverFactory, )
    {
        $this->driver= $driverFactory->get('default');
    }

    public function consumeMessage($data, AMQPMessage $message): string
    {
        $orderId = $data['id'];
        $delay = $data['expires_at'] - (new DateTime())->getTimestamp();

        // The `ExampleJob` here will be serialized and stored in Redis, so internal variables of the object are best passed only normal data.
        // Similarly, if the annotation is used internally, @Value will serialize the corresponding object, causing the message body to become larger.
        // So it is NOT recommended to use the `make` method to create a `Job` object.
        $this->driver->push(new OrderCompleteJob($orderId), $delay);

        return Result::ACK;
    }
}
