<?php

declare (strict_types=1);

namespace Payments\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property $id
 * @property $version
 * @property $user_id
 * @property $price
 * @property $status
 */
class Order extends Model
{
    use Version;

    public const STATUS_CREATED = 'created';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_AWAITING_PAYMENT = 'awaiting:payment';
    public const STATUS_COMPLETE = 'complete';

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
    protected $fillable = ['id', 'user_id', 'status', 'price', 'version'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'price' => 'integer', 'version' => 'integer'];
}