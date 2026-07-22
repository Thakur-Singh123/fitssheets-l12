<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserCasemanagerRel extends Model
{
    protected $fillable = [
        'casemanager_id','users_id'
     ];
}
