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
 * Helper class for retrieving language translation strings.
 * 
 * @author Jalil Fahimi <jalilfahimi535@gmail.com>
 */
final class LTR
{
    /**
     *      
     * 
     * @param  string  $identifier
     * @param  string  $lang
     * @return string
     *
     */
    private static function exists(string $identifier, string $lang): bool
    {
        require_once(dirname(__DIR__, 2) . "/lang/$lang.php");
        
        $class = strtoupper($lang);
        if (!class_exists($class)) {
            return false;
        }

        $instance = new $class();
        $string = $instance::strings();

        return (array_key_exists($identifier, $string));
    }

    /**
     * Returns the string value of the given identifier from the given language.
     * If language is not allowed or found, the default language will be used.
     * This function also changes the placeholders to the given values.
     * 
     * Example of using placeholders: 
     *          $placeholder = new PlaceHolder('email', 'test_email');
     *          $string = LTR::get('identifier', 'en', [$placeholder]);
     *      
     * @param  string  $identifier
     * @param  string  $lang
     * @param  PlaceHolder[]  $placeholders
     * @return string
     *
     */
    public static function get(string $identifier, string $lang = 'en', array $placeholders = []): string
    {
        if (file_exists(dirname(__DIR__, 2) . "/lang/$lang.php") && strtolower($lang) !== 'en') {
            require_once(dirname(__DIR__, 2) . "/lang/$lang.php");
        } else {
            $lang = "en";
            require_once(dirname(__DIR__, 2) . "/lang/$lang.php");
        }

        $class = strtoupper($lang);
        if (!class_exists($class)) {
            return "FATAL: class '$class' does not exist.";
        }

        $instance = new $class();
        $string = $instance::strings();

        if (array_key_exists($identifier, $string)) {
            return self::convert_placeholders($string[$identifier], $lang, $placeholders);
        }

        $placeholders = [new PlaceHolder('identifier', $identifier), new PlaceHolder('filename', "lang/$lang.php")];
        $msg = self::get('missinglangidentifier', $lang, $placeholders);
        Log::warning($msg);

        if (strtolower($lang) !== 'en' && self::exists($identifier, 'en')) {
            return self::get($identifier, 'en', $placeholders);
        }

        return $msg;
    }

    /**
     * 
     *      
     * @param  string  $raw
     * @param  string  $lang
     * @param  PlaceHolder[] $placeholders
     * @return string
     *
     */
    private static function convert_placeholders(string $raw, string $lang, array $placeholders = []): string
    {
        $string = $raw;
        if ($placeholders) {
            foreach ($placeholders as $placeholder) {
                $to_replace = '${' . $placeholder->key . '}';
                $replace_val = "'$placeholder->value'";
                if (str_contains($string, $to_replace)) {
                    $string = str_replace($to_replace, $replace_val, $string);
                }
            }
        }

        if (preg_match_all('/\\${(.*?)}/', $string, $matches)) {
            $placeholders = [new PlaceHolder('placeholder', $matches[1][0])];
            $msg =  self::get('missinglangplaceholder', $lang, $placeholders);
            Log::warning($msg);
            return $msg;
        }

        return $string;
    }
}

/**
 * Type definition for placeholders for language strings.
 * 
 * @author Jalil Fahimi <jalilfahimi535@gmail.com>
 */
final class PlaceHolder
{
    /**
     * The key of the placeholder
     *
     * @var string
     */
    public $key = '';

    /**
     * The value of the placeholder
     *
     * @var string
     */
    public $value = '';

    /**
     * 
     * 
     * @param string  $key
     * @param string  $value
     * 
     */
    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }
}
