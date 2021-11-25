<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pledges extends Model
{
    protected $table = "pledges";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'amount', 'amount_paid', 'interest', 'status',
        'when_due', 'created_at', 'updated_at', 'referrer_id', 'referrer_bonus',
        'process', 'paid_bonus', 'bonus_to_collect', 'can_withdraw',
        'amount_received_and_confirmed', 'amount_paid_confirmed','sent_recommit_sms'
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
