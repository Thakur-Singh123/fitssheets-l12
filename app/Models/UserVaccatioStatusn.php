<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVaccatioStatusn extends Model
{
    protected $fillable = [
        'user_id', 'vacc_start', 'vacc_end','vacc_comments','vacc_top', 'vacc_rbu', 'vacc_status'
    ];
    
}
