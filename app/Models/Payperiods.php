<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payperiods extends Model
{
     protected $fillable = [
        'companies_id', 'payperiod', 'payperiod_value'
    ];
	
	/**
     * Get the post that owns the comment.
     */
    public function companies()
    {
        return $this->belongsTo('App\Models\Company');
    }
	
}
