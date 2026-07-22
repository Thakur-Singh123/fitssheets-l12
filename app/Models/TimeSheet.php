<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSheet extends Model
{
     protected $fillable = [
        'companies_id', 'houses_id', 'time_in','time_out','hours_wrk', 'hours_day', 'hours_price', 'remarks', 'vacation_status' , 'users_id', 'approve','approved_by', 'approved_at', 'cm_id','cmcheck_status', 'cm_update_at'
    ];
	
	/**
     * Get the post that owns the comment.
     */
    public function companies()
    {
        return $this->belongsTo('App\Models\Company');
    }
	
	/**
     * Get the post that owns the comment.
     */
    public function users()
    {
        return $this->belongsTo('App\Models\User');
    }
	
	/**
     * Get the post that owns the comment.
     */
    public function houses()
    {
        return $this->belongsTo('App\Models\House');
    }
}
