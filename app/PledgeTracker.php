<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PledgeTracker extends Model
{
    protected $table = "pledge_tracker";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'pledge_id', 'amount', 'user_sending', 'user_receiving', 'status',
        'proof', 'updated_at', 'created_at', 'pledge_id_user_sending', 'pledge_id_bonus',
        'bonus'
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
