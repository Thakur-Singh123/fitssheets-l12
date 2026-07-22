<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $fillable = [
        'companies_id', 'house_add'
    ];
	
	
	/**
     * Get the post that owns the comment.
     */
    public function companies()
    {
        return $this->belongsTo('App\Models\Company');
    }
}
