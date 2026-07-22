<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'users_id', 'company_id', 'phone_no', 'message', 'status'
    ];
}
