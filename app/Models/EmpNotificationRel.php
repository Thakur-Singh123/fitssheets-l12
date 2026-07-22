<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpNotificationRel extends Model
{
    protected $fillable = [
        'users_id','emp_notfications_id'
    ];
	
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
    public function emp_notfications()
    {
        return $this->belongsTo('App\Models\EmpNotfication');
    }
}
