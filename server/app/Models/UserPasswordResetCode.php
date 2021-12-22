<?php

namespace App\Models;

use CFG;
use CORE;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class UserPasswordResetCode extends Model
{
    use HasFactory, Prunable;
    public $timestamps = true;

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        $cfg = CFG::get('pruneuserpasswordresetcodesafter');
        if (!CORE::isNumber($cfg)) {
            CFG::set('pruneuserpasswordresetcodesafter', 15);
            $cfg = 15;
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
        'user',
        'code',
    ];
}
