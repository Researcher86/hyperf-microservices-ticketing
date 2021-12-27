<?php

declare (strict_types=1);

namespace Orders\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property $id
 * @property $title
 * @property $price
 */
class Ticket extends Model
{
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tickets';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'title', 'price', 'version'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'price' => 'integer', 'version' => 'integer'];
}