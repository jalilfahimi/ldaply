<?php
// This file is part of ldaply - https://github.com/thesaintboy/ldaply

/**
 *
 *
 * @author Jalil Fahimi (jalilfahimi535@gmail.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

use App\Models\Configuration;
use App\Models\PluginConfiguration;

/**
 * Helper class for configurations.
 * 
 * @author Jalil Fahimi <jalilfahimi535@gmail.com>
 */
final class CFG
{
    /**
     * Checks whether a key in global configuration table
     * or the plugin_configurations table is set.
     * 
     *
     * @param string $key the key to get
     * @param string $plugin (optional) the plugin scope, default null
     * @return bool
     */
    public static function exists(string $key, string $plugin = null): bool
    {
        if (empty($plugin)) {
            $item = Configuration::where('key', '=', $key)->first();
            return (!empty($item));
        } else {
            $item = PluginConfiguration::where('key', '=', $key)->where('plugin', '=', $plugin)->first();
            return (!empty($item));
        }
    }

    /**
     * Get a key in global configuration table
     * or the plugin_configurations table.
     * 
     *
     * @param string $key the key to get
     * @param string $plugin (optional) the plugin scope, default null
     * @return string
     */
    public static function get(string $key, string $plugin = null): string
    {
        if (empty($plugin)) {
            $item = Configuration::where('key', '=', $key)->first();
            if (empty($item)) {
                return '';
            }
        } else {
            $item = PluginConfiguration::where('key', '=', $key)->where('plugin', '=', $plugin)->first();
            if (empty($item)) {
                return '';
            }
        }

        return $item->value;
    }

    /**
     * Set a key in global configuration table
     * or the plugin_configurations table.
     *
     * @param string $key the key to set
     * @param string $value the value to set (without magic quotes)
     * @param string $plugin (optional) the plugin scope, default null
     * @return bool true or exception
     */
    public static function set(string $key, string $value, string $plugin = null): void
    {
        if (empty($plugin)) {
            Configuration::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        } else {
            PluginConfiguration::updateOrCreate(
                ['plugin' => $plugin, 'key' => $key],
                ['value' => $value]
            );
        }
    }

    /**
     * Unset a key in global configuration table
     * or the plugin_configurations table.
     *
     * @param string $key the key to set
     * @param string $plugin (optional) the plugin scope, default null
     * @return bool true or exception
     */
    public static function unset(string $key, string $plugin = null): void
    {
        if (empty($plugin)) {
            $item = Configuration::where('key', '=', $key)->first();
            if (!empty($item)) {
                $item->forceDelete();
            }
        } else {
            $item = PluginConfiguration::where('key', '=', $key)->where('plugin', '=', $plugin)->first();
            if (!empty($item)) {
                $item->forceDelete();
            }
        }
    }
}
