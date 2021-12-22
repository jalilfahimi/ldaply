<?php

namespace App\Models;

use CFG;
use CORE;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory, Prunable;
    public $timestamps = true;

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        $cfg = CFG::get('pruneusersessionsafter');
        if (!CORE::isNumber($cfg)) {
            CFG::set('pruneusersessionsafter', 120);
            $cfg = 120;
        } else {
            $cfg = intval($cfg);
        }

        return static::where('created_at', '<=', now()->subMinutes($cfg));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'session',
        'user',
    ];
}
