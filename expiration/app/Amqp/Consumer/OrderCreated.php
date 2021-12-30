<?php

declare(strict_types=1);

namespace Expiration\Amqp\Consumer;

use DateTime;
use Expiration\Amqp\Producer\ExpirationComplete;
use Expiration\Job\OrderCompleteJob;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Producer;
use Hyperf\Amqp\Result;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Swoole\Timer;

#[Consumer(exchange: 'orders', routingKey: 'order-created', queue: 'expiration-order-created', name: "OrderCreated", nums: 1)]
class OrderCreated extends ConsumerMessage
{
    private DriverInterface $driver;
    private Producer $producer;

    public function __construct(DriverFactory $driverFactory, Producer $producer)
    {
        $this->driver= $driverFactory->get('default');
        $this->producer = $producer;
    }

    public function consumeMessage($data, AMQPMessage $message): string
    {
        $orderId = $data['id'];
        $delay = $data['expires_at'] - (new DateTime())->getTimestamp();

        Timer::after($delay * 1000, function () use ($orderId) {
            var_dump("ExpirationComplete orderId=" . $orderId);
            $this->producer->produce(new ExpirationComplete($orderId));
        });

//        swoole_timer_after($delay * 1000, function () use ($orderId) {
//            var_dump("ExpirationComplete orderId=" . $orderId);
//            $this->producer->produce(new ExpirationComplete($orderId));
//        });

        // The `ExampleJob` here will be serialized and stored in Redis, so internal variables of the object are best passed only normal data.
        // Similarly, if the annotation is used internally, @Value will serialize the corresponding object, causing the message body to become larger.
        // So it is NOT recommended to use the `make` method to create a `Job` object.
//        $this->driver->push(new OrderCompleteJob($orderId), $delay);

        return Result::ACK;
    }
}
