<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;
    public $timestamps = true;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'firstname',
        'lastname',
        'lastname',
        'auth',
        'calendartype',
        'timezone',
        'language',
        'firstaccess',
        'lastaccess',
        'lastlogin',
        'lastip',
        'mobile',
        'fax',
        'address',
        'city',
        'country',
    ];
}
