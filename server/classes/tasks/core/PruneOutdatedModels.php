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
use Illuminate\Support\Facades\Artisan;


final class PruneOutdatedModels extends BaseTask
{
    /**
     * Return the default description
     * 
     * @return string
     */
    public static function description(): string
    {
        return 'Prunes outdated models.';
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
            $this->log('Started deleting outdated models.');
            Artisan::call('model:prune');
            $this->log('Finished.');
        } catch (Exception $e) {
            $this->log($e);
        }
    }
}
