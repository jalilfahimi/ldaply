<?php
// This file is part of ldaply - https://github.com/thesaintboy/ldaply

/**
 *
 *
 * @author Jalil Fahimi (jalilfahimi535@gmail.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */


abstract class ITranslation
{
    /**
     * Returns the actual translations.
     * 
     * @return array
     */
    abstract static function strings(): array;
}
