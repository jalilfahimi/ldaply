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

final class ExampleTask extends BaseTask
{
    /**
     * Return the default description
     * 
     * @return string
     */
    public static function description(): string
    {
        return '';
    }

    /**
     * Return the default interval in seconds
     * 
     * @return int
     */
    public static function interval(): int
    {
        return 24 * 60 * 60;
    }

    /**
     * Return the default enabled
     * 
     * @return bool
     */
    public static function enabled(): bool
    {
        return false;
    }

    /**
     * Do the logic here.
     * 
     * @return void
     */
    public function execute(): void
    {
        try {
        } catch (Exception $e) {
            $this->log($e);
        }
    }
}
