<?php
// This file is part of ldaply - https://github.com/thesaintboy/ldaply

/**
 *
 *
 * @author Jalil Fahimi (jalilfahimi535@gmail.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

use Illuminate\Support\Facades\Log;

/**
 * Helper class for general lib functions.
 * 
 * @author Jalil Fahimi <jalilfahimi535@gmail.com>
 */
final class CORE
{
    /**
     *
     *
     * 
     * @return bool
     */
    public static function isInMaintenanceMode(): bool
    {
        return (intval(CFG::get('maintenancemode')) === 1);
    }

    /**
     *
     *
     * 
     * @return bool
     */
    public static function CanGoInMaintenanceMode(): bool
    {
        return (self::cronIsRunning() === false);
    }

    /**
     *
     *
     * 
     * @return void
     */
    public static function StartMaintenanceMode(): void
    {
        CFG::set('maintenancemode', '1');
    }

    /**
     *
     *
     * 
     * @return void
     */
    public static function StopMaintenanceMode(): void
    {
        CFG::set('maintenancemode', '0');
    }

    /**
     *
     *
     * 
     * @return bool
     */
    public static function cronIsRunning(): bool
    {
        return (intval(CFG::get('cronisstarted')) === 1);
    }

    /**
     *
     *
     * 
     * @return void
     */
    public static function startCron(): void
    {
        CFG::set('cronisstarted', '1');
    }

    /**
     *
     *
     * 
     * @return void
     */
    public static function stopCron(): void
    {
        CFG::set('cronisstarted', '0');
    }

    /**
     * Return true if given value is integer or string with integer value
     *
     * @param mixed $value String or Int
     * @return bool true if number, false if not
     */
    public static function isNumber($value): bool
    {
        if (is_int($value)) {
            return true;
        } else if (is_string($value)) {
            return ((string)(int)$value) === $value;
        } else {
            return false;
        }
    }

    /**
     *
     * @param mixed $data
     * @return void
     */
    public static function debug($data): void
    {
        echo '<pre>' . var_export($data, true) . '</pre>';
        echo PHP_EOL;
    }

    /**
     *
     * @param array $values
     * @return void
     */
    public static function logcli(...$values): void
    {
        foreach ($values as $value) {
            echo var_dump($value);
            echo PHP_EOL;
        }
    }
}
