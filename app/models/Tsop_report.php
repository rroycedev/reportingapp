<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Tsop_report extends Model
{
    protected $table = 'tsop.tsop_report';

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'help_url', 'password','day_in_title','ignore_replication','date_created','last_updated'
    ];

    protected $casts = [
        'date_created' => 'datetime',
        'last_updated' => 'datetime',
    ];    

}
