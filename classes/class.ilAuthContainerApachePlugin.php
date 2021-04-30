<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once 'Auth/Container.php';

/**
 * Description of class
 *
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 */
class ilAuthContainerApachePlugin extends Auth_Container
{
    /**
     * @var ilComponentLogger|null
     */
    private $logger = null;

    /**
     * @var string
     */
    private $valid_user = '';
    
    /**
     *
     * @var ilApacheAuthPluginSettings|null
     */
    private $settings = null;

    /**
     * @var bool
     */
    private static $force_creation = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logger = ilLoggerFactory::getLogger('lfskyauth');
        $this->logger->debug('Called plugin construct');
        
        $this->settings = ilApacheAuthPluginSettings::getInstance();
        
        parent::__construct();
    }
    
    
    public function forceCreation(bool $a_status)
    {
        self::$force_creation = $a_status;
    }
    
    
    /**
     * Fetch data
     * @param string $username
     * @param string $password
     * @param bool $isChallengeResponse
     */
    public function fetchData(string $username, string $password, bool $isChallengeResponse = false) : bool
    {
        if (!isset($_SERVER[$this->settings->getIndicatorName()])) {
            $this->logger->info('Authentication failed: no input for indicator value found');
            return false;
        }
        if (strcasecmp($_SERVER[$this->settings->getIndicatorName()], $this->settings->getIndicatorValue()) !== 0) {
            $this->logger->info($this->settings->getIndicatorValue() . ' does not match ' . $_SERVER[$this->settings->getIndicatorName()]);
            return false;
        }
        
        $uname = $_SERVER[$this->settings->getUsernameField()];
        $this->logger->debug('Original username: ' . $uname);
        if (!strlen($uname)) {
            $this->logger->info('No username given');
            return false;
        }
        if (strpos($uname, '@') === false) {
            $uname_without_domain = $uname;
        } else {
            $uname_without_domain = substr($uname, 0, strpos($uname, '@'));
        }
        $this->logger->debug('Shortened username: ' . $uname_without_domain);
        
        // check for external account
        include_once './Services/LDAP/classes/class.ilLDAPServer.php';
        $servers = ilLDAPServer::_getActiveServerList();
        foreach ($servers as $server_id) {
            $ext_account = ilObjUser::_checkExternalAuthAccount('ldap_' . $server_id, $uname_without_domain);
            if ($ext_account) {
                $this->logger->info('Found ILIAS login name "' . $ext_account . '" for user: ' . $uname_without_domain);
                $this->valid_user = $ext_account;
                break;
            }
        }
        
        if ($this->valid_user) {
            $this->logger->info('Successfully authenticated ILIAS user: ' . $this->valid_user);
            return true;
        }
        return false;
    }
    
    /**
     * Login observer
     * @param string $a_username
     * @param string $a_auth
     * @return boolean
     */
    public function loginObserver(string $a_username, string $a_auth) : bool
    {
        $this->logger->debug('Login observer called for: ' . $this->valid_user);
        $a_auth->setAuth($this->valid_user);
        return true;
    }
}
