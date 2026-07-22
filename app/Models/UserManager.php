<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserManager extends Model
{
    protected $fillable = [
        'musers_id', 'users_id'
    ];
	
	/**
     * Get the post that owns the comment.
     */
    public function users()
    {
        return $this->belongsTo('App\Models\User');
    }
}
