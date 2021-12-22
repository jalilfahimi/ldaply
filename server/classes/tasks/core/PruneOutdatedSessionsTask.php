<?php
// This file is part of ldaply - https://github.com/thesaintboy/ldaply

/**
 *
 *
 * @author Jalil Fahimi (jalilfahimi535@gmail.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

namespace Core\ScheduledTask;

use Exception;
use CFG;
use CORE;
use Illuminate\Support\Facades\DB;


final class PruneOutdatedSessionsTask extends BaseTask
{
    /**
     * Return the default description
     * 
     * @return string
     */
    public static function description(): string
    {
        return 'Prunes outdated sessions.';
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
            $cfg = CFG::get('pruneusersessionsafter');

            if (!CORE::isNumber($cfg)) {
                CFG::set('pruneusersessionsafter', 120);
                $cfg = 120;
            } else {
                $cfg = intval($cfg);
                if ($cfg <= 0) {
                    CFG::set('pruneusersessionsafter', 120);
                    $cfg = 120;
                }
            }

            $this->log('Started deleting outdated sessions.');
            DB::table('sessions')->where('created_at', '<=', now()->subMinutes($cfg))->delete();
            $this->log('Finished.');
        } catch (Exception $e) {
            $this->log($e);
        }
    }
}
