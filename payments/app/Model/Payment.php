<?php

declare (strict_types=1);

namespace Payments\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property $id
 * @property $order_id
 * @property $payment_id
 */
class Payment extends Model
{
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payments';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'order_id', 'payment_id'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'order_id' => 'integer'];

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}