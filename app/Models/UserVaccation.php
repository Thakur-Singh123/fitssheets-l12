<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVaccation extends Model
{
   protected $fillable = [
        'user_id', 'vacc_sl', 'vacc_vc', 'vacc_be', 'vacc_jd', 'vacc_frm', 'vacc_to', 'vacc_aprby'
    ];
    
}
