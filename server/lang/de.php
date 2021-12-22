<?php
// This file is part of ldaply - https://github.com/thesaintboy/ldaply

/**
 *
 *
 * @author Jalil Fahimi (jalilfahimi535@gmail.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once('lib.php');

/**
 * Language translations for German.
 * 
 * @author Jalil Fahimi (jalilfahimi535@gmail.com)
 */
final class DE extends ITranslation
{
    /**
     * Returns the actual translations.
     * 
     * @return array
     */
    public static function strings(): array
    {
        return [
            'default' => 'Default',
            'missinglangidentifier' => 'ACHTUNG: Identifier ${identifier} fehlt in ${filename}.',
            'missinglangplaceholder' => 'ACHTUNG: Platzhalter ${placeholder} konnte nicht konvertiert werden. Achten Sie darauf, die Platzhalter richtig zu übergeben.',
        ];
    }
}
