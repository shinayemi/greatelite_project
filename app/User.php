<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'email', 'confirmed_email', 'password', 'mobile', 'confirm_mobile', 'status', 'registration_fee',
        'first_name', 'middle_name', 'last_name', 'sex', 'email_confirmation_key', 'user_ref', 'referrer',
        'profile_picture', 'account_type', 'confirmed_by', 'send_missed_merged_sms', 'agreed_to_terms', 'created_at'
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at'
    ];
}
