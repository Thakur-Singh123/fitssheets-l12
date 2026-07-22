<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProblem extends Model
{
     protected $fillable = [
        'user_id', 'companies_id', 'ssn', 'name','issue','resolution_remarks'
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
	
}	