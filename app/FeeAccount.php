<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeeAccount extends Model
{
    protected $table = "fee_accounts";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'first_name', 'last_name', 'bank', 'account_number',
        'account_name', 'telephone', 'updated_at', 'created_at'
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
