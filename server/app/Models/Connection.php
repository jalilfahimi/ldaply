<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'host',
        'base_dn',
        'bind_dn',
        'port',
        'tls',
        'version',
        'encoding',
        'pagesize',
        'pagedresultscontrol',
        'private',
        'user',
    ];
}
