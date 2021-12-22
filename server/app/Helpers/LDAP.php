<?php
// This file is part of ldaply - https://github.com/thesaintboy/ldaply

/**
 *
 *
 * @author Jalil Fahimi (jalilfahimi535@gmail.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

use App\Models\User;
use Illuminate\Support\Facades\Log;
use LdapRecord\Connection;

// rootDSE is defined as the root of the directory data tree on a directory server.
if (!defined('LDAP_DEFAULT_ROOTDSE')) {
    define('LDAP_DEFAULT_ROOTDSE', '');
}

// Paged results control OID value.
if (!defined('LDAP_DEFAULT_PAGED_RESULTS_CONTROL')) {
    define('LDAP_DEFAULT_PAGED_RESULTS_CONTROL', '1.2.840.113556.1.4.319');
}

// Default page size when using LDAP paged results
if (!defined('LDAP_DEFAULT_PAGESIZE')) {
    define('LDAP_DEFAULT_PAGESIZE', 250);
}
if (!defined('LDAP_DEFAULT_VERSION')) {
    define('LDAP_DEFAULT_VERSION', 3);
}
if (!defined('LDAP_DEFAULT_TLS')) {
    define('LDAP_DEFAULT_TLS', false);
}
if (!defined('LDAP_DEFAULT_PORT')) {
    define('LDAP_DEFAULT_PORT', 389);
}
if (!defined('LDAP_DEFAULT_ENCODING')) {
    define('LDAP_DEFAULT_ENCODING', 'utf-8');
}

// See http://support.microsoft.com/kb/305144 to interprete these values.
if (!defined('AUTH_AD_ACCOUNTDISABLE')) {
    define('AUTH_AD_ACCOUNTDISABLE', 0x0002);
}
if (!defined('AUTH_AD_NORMAL_ACCOUNT')) {
    define('AUTH_AD_NORMAL_ACCOUNT', 0x0200);
}
if (!defined('AUTH_NTLMTIMEOUT')) {  // timewindow for the NTLM SSO process, in secs...
    define('AUTH_NTLMTIMEOUT', 10);
}

// UF_DONT_EXPIRE_PASSWD value taken from MSDN directly
if (!defined('UF_DONT_EXPIRE_PASSWD')) {
    define('UF_DONT_EXPIRE_PASSWD', 0x00010000);
}

// The Posix uid and gid of the 'nobody' account and 'nogroup' group.
if (!defined('AUTH_UID_NOBODY')) {
    define('AUTH_UID_NOBODY', -2);
}
if (!defined('AUTH_GID_NOGROUP')) {
    define('AUTH_GID_NOGROUP', -2);
}

// Regular expressions for a valid NTLM username and domain name.
if (!defined('AUTH_NTLM_VALID_USERNAME')) {
    define('AUTH_NTLM_VALID_USERNAME', '[^/\\\\\\\\\[\]:;|=,+*?<>@"]+');
}
if (!defined('AUTH_NTLM_VALID_DOMAINNAME')) {
    define('AUTH_NTLM_VALID_DOMAINNAME', '[^\\\\\\\\\/:*?"<>|]+');
}
// Default format for remote users if using NTLM SSO
if (!defined('AUTH_NTLM_DEFAULT_FORMAT')) {
    define('AUTH_NTLM_DEFAULT_FORMAT', '%domain%\\%username%');
}
if (!defined('AUTH_NTLM_FASTPATH_ATTEMPT')) {
    define('AUTH_NTLM_FASTPATH_ATTEMPT', 0);
}
if (!defined('AUTH_NTLM_FASTPATH_YESFORM')) {
    define('AUTH_NTLM_FASTPATH_YESFORM', 1);
}
if (!defined('AUTH_NTLM_FASTPATH_YESATTEMPT')) {
    define('AUTH_NTLM_FASTPATH_YESATTEMPT', 2);
}

// Allows us to retrieve a diagnostic message in case of LDAP operation error
if (!defined('LDAP_OPT_DIAGNOSTIC_MESSAGE')) {
    define('LDAP_OPT_DIAGNOSTIC_MESSAGE', 0x0032);
}

if (!defined('LDAP_DN_SPECIAL_CHARS')) {
    define('LDAP_DN_SPECIAL_CHARS', 0);
}
if (!defined('LDAP_DN_SPECIAL_CHARS_QUOTED_NUM')) {
    define('LDAP_DN_SPECIAL_CHARS_QUOTED_NUM', 1);
}
if (!defined('LDAP_DN_SPECIAL_CHARS_QUOTED_ALPHA')) {
    define('LDAP_DN_SPECIAL_CHARS_QUOTED_ALPHA', 2);
}
if (!defined('LDAP_DN_SPECIAL_CHARS_QUOTED_ALPHA_REGEX')) {
    define('LDAP_DN_SPECIAL_CHARS_QUOTED_ALPHA_REGEX', 3);
}

/**
 * Type definition for LDAP configuration schema.
 * 
 * @author Jalil Fahimi <jalilfahimi535@gmail.com>
 */
final class LDAPCFG
{
    /**
     *
     * @var string
     */
    public $host = '';

    /**
     *
     * @var string
     */
    public $base_dn = '';

    /**
     *
     * @var string
     */
    public $bind_dn = '';

    /**
     *
     * @var string
     */
    public $bind_pw = '';

    /**
     *
     * @var int
     */
    public $port = LDAP_DEFAULT_PORT;

    /**
     *
     * @var bool
     */
    public $tls = LDAP_DEFAULT_TLS;

    /**
     *
     * @var int
     */
    public $version = LDAP_DEFAULT_VERSION;

    /**
     *
     * @var string
     */
    public $encoding = LDAP_DEFAULT_ENCODING;

    /**
     *
     * @var int
     */
    public $pagesize = LDAP_DEFAULT_PAGESIZE;

    /**
     *
     * @var string
     */
    public $pagedresultscontrol = LDAP_DEFAULT_PAGED_RESULTS_CONTROL;

    /**
     *
     * @var string
     */
    public $rootdse = LDAP_DEFAULT_ROOTDSE;

    /**
     *
     * @var bool
     */
    private $anonymous = false;

    /**
     * 
     * @param string    $host
     * @param string    $base_dn
     * @param string    $bind_dn
     * @param string    $bind_pw
     * @param int       $port
     * @param bool      $tls
     * @param int       $version
     * @param string    $encoding
     * @param int       $pagesize
     * @param string    $pagedresultscontrol
     * @param string    $rootdse
     * 
     * @author Jalil Fahimi <jalilfahimi535@gmail.com>
     */
    public function __construct(
        string $host,
        string $base_dn,
        string $bind_dn = '',
        string $bind_pw = '',
        int $port = LDAP_DEFAULT_PORT,
        bool $tls = LDAP_DEFAULT_TLS,
        int $version = LDAP_DEFAULT_VERSION,
        string $encoding = LDAP_DEFAULT_ENCODING,
        int $pagesize = LDAP_DEFAULT_PAGESIZE,
        string $pagedresultscontrol = LDAP_DEFAULT_PAGED_RESULTS_CONTROL,
        string $rootdse = LDAP_DEFAULT_ROOTDSE
    ) {
        if (empty($host) || strlen($host) < 1) {
            throw new Exception('Invalid host.');
        }
        $this->host = $host;

        if (empty($base_dn) || strlen($base_dn) < 1) {
            throw new Exception('Invalid base_dn.');
        }
        $this->base_dn = $base_dn;

        if (empty($bind_dn) || empty($bind_pw) || strlen($bind_dn) < 1 || strlen($bind_pw) < 1) {
            $this->anonymous = true;
        } else {
            $this->bind_dn = $bind_dn;
            $this->bind_pw = $bind_pw;
        }

        if ($port) {
            $this->port = $port;
        }
        if ($tls) {
            $this->tls = $tls;
        }
        if ($version) {
            $this->version = $version;
        }
        if ($encoding) {
            $this->encoding = $encoding;
        }
        if ($pagesize) {
            $this->pagesize = $pagesize;
        }
        if ($pagedresultscontrol) {
            $this->pagedresultscontrol = $pagedresultscontrol;
        }
        if ($rootdse) {
            $this->rootdse = $rootdse;
        }
    }

    public function anonymous(): bool
    {
        return $this->anonymous;
    }
}


/**
 * Helper class for LDAP.
 * 
 * @author Jalil Fahimi <jalilfahimi535@gmail.com>
 */
final class LDAP
{
    /**
     *
     * @var LDAPCFG
     */
    private $config;

    /**
     *
     * @var User
     */
    private $user;

    /**
     *
     * @var object
     */
    private $connection;

    /**
     *
     * @var bool
     */
    private $initialized = false;

    /**
     * 
     * 
     * @param LDAPCFG $cfg
     * @param User    $user
     */
    public function __construct(LDAPCFG $cfg, User $user)
    {
        $this->config = $cfg;
        $this->user = $user;
    }

    /**
     * 
     * 
     * @return object
     */
    public function connect(): object
    {
        if (!$this->initialized) {
            $this->init();
        }
        try {
            $conn = $this->connection->connect();
            if (!$this->config->anonymous()) {
                try {
                    $this->connection->auth()->attempt($this->config->bind_dn, $this->config->bind_pw, false);
                } catch (\LdapRecord\Auth\BindException $e) {
                    $error = $e->getDetailedError();
                    $msg = $error->getErrorMessage();
                    Log::debug($msg);
                    throw new Exception($msg);
                }
            }
            return $conn;
        } catch (\LdapRecord\Auth\BindException $e) {
            $error = $e->getDetailedError();
            $msg = $error->getErrorMessage();
            Log::debug($msg);
            throw new Exception($msg);
        }
    }

    /**
     * 
     * 
     * @return string
     */
    public function test(): string
    {
        if (!function_exists('ldap_connect')) {
            return LTR::get('ldapnoextension', $this->user->language);
        }
        if (!$this->initialized) {
            $this->init();
        }
        try {
            $this->connection->connect();
            if (!$this->config->anonymous()) {
                try {
                    $this->connection->auth()->attempt($this->config->bind_dn, $this->config->bind_pw, false);
                } catch (\LdapRecord\Auth\BindException $e) {
                    $error = $e->getDetailedError();
                    $msg = $error->getErrorMessage();
                    Log::debug($msg);
                    return $msg;
                }
            }
            return LTR::get('connectingldapsuccess', $this->user->language);
        } catch (\LdapRecord\Auth\BindException $e) {
            $error = $e->getDetailedError();
            $msg = $error->getErrorMessage();
            Log::debug($msg);
            return $msg;
        }
    }

    /**
     * 
     *
     * @return void
     */
    private function init(): void
    {
        $this->connection = new Connection([
            // Mandatory Configuration Options
            'hosts'            => [$this->config->host],
            'base_dn'          => $this->config->base_dn,
            'username'         => $this->config->anonymous() ? null : $this->config->bind_dn,
            'password'         => $this->config->anonymous() ? null : $this->config->bind_pw,

            // Optional Configuration Options
            'port'             => $this->config->port,
            'use_ssl'          => false,
            'use_tls'          => $this->config->tls,
            'version'          => $this->config->version,
            'timeout'          => 5,
            'follow_referrals' => false,

            // Custom LDAP Options
            'options' => [
                // See: http://php.net/ldap_set_option
                LDAP_OPT_X_TLS_REQUIRE_CERT => LDAP_OPT_X_TLS_HARD
            ]
        ]);

        $this->initialized = true;
    }
}
