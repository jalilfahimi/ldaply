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
 * Language translations for English.
 * 
 * @author Jalil Fahimi (jalilfahimi535@gmail.com)
 */
final class EN extends ITranslation
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
            'missinglangidentifier' => 'WARNING: Identifier ${identifier} is missing in ${filename}.',
            'missinglangplaceholder' => 'WARNING: Placeholder ${placeholder} could not be converted. Make sure to pass the placeholders properly.',
            'loginfail' => 'Invalid email and/or password. Please reenter your credentials.',
            'ldapnoextension' => 'The PHP LDAP module does not seem to be present. Please ensure it is installed and enabled.',
            'ldapnotconfigured' => 'The LDAP host url is currently not configured.',
            'connectingldapsuccess' => 'Connecting to your LDAP server was successful.',
            'pagedresultsnotsupp' => 'LDAP paged results not supported (either your PHP version lacks support, you have configured the connection to use LDAP protocol version 2 or we cannot contact your LDAP server to see if paged support is available.)',
        ];
    }
}
