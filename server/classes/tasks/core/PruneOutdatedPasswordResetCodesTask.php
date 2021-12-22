<?php
// This file is part of ldaply - https://github.com/thesaintboy/ldaply

/**
 *
 *
 * @author Jalil Fahimi (jalilfahimi535@gmail.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

namespace Core\ScheduledTask;

use CFG;
use CORE;
use Exception;
use Illuminate\Support\Facades\DB;


final class PruneOutdatedPasswordResetCodesTask extends BaseTask
{
    /**
     * Return the default description
     * 
     * @return string
     */
    public static function description(): string
    {
        return 'Prunes outdated password reset codes.';
    }

    /**
     * Return the default interval in seconds
     * 
     * @return int
     */
    public static function interval(): int
    {
        return 0;
    }

    /**
     * Return the default enabled
     * 
     * @return bool
     */
    public static function enabled(): bool
    {
        return true;
    }

    /**
     * Do the logic here.
     * 
     * @return void
     */
    public function execute(): void
    {
        try {
            $cfg = CFG::get('pruneuserpasswordresetcodesafter');
            
            if (!CORE::isNumber($cfg)) {
                CFG::set('pruneuserpasswordresetcodesafter', 15);
                $cfg = 15;
            } else {
                $cfg = intval($cfg);
                if ($cfg <= 0) {
                    CFG::set('pruneuserpasswordresetcodesafter', 15);
                    $cfg = 15;
                }
            }
            
            $this->log('Started deleting outdated password reset codes.');
            DB::table('user_password_reset_codes')->where('created_at', '<=', now()->subMinutes($cfg))->delete();
            $this->log('Finished.');
        } catch (Exception $e) {
            $this->log($e);
        }
    }
}
