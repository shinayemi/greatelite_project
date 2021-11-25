<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PledgeOptions extends Model
{
    protected $table = "pledge_options";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'amount', 'time_added', 'time_updated'
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
