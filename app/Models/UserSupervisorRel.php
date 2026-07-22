<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class UserSupervisorRel extends Model
{
    protected $fillable = [
       'users_id','supervisor_id'
    ];
}
