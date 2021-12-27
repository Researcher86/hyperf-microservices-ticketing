<?php

declare (strict_types=1);

namespace Orders\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property $id
 * @property $user_id
 * @property $ticket_id
 * @property $status
 * @property $expires_at
 * @property $version
 */
class Order extends Model
{
    use Version;

    public const STATUS_CREATED = 'created';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_AWAITING_PAYMENT = 'awaiting:payment';
    public const STATUS_COMPLETE = 'complete';

    public const STATUS_ACTIVE = [
        self::STATUS_CREATED,
        self::STATUS_AWAITING_PAYMENT,
        self::STATUS_COMPLETE,
    ];

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'ticket_id', 'status', 'expires_at', 'version'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'ticket_id' => 'integer', 'version' => 'integer', 'expires_at' => 'timestamp'];

    public function ticket()
    {
        return $this->hasOne(Ticket::class, 'id', 'ticket_id');
    }
}