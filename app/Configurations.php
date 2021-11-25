<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Configurations extends Model
{
    protected $table = "configurations";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'value', 'created_at', 'updated_at'
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
