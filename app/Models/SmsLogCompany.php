<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLogCompany extends Model
{
    protected $fillable = [
        'sms_log_id', 'company_id'
    ];
}
