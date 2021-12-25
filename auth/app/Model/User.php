<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property $id
 * @property $email
 * @property $password
 */
class User extends Model
{
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'email', 'password'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['password'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer'];
}