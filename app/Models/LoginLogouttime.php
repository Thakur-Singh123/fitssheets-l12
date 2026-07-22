<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLogouttime extends Model
{
     protected $fillable = [
        'users_id', 'last_login_at', 'last_logout_at'];

    //Function for get user
    public function user() {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
