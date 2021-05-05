<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once './Services/Authentication/classes/class.ilAuthPlugin.php';
include_once './Services/Authentication/interfaces/interface.ilAuthDefinition.php';


/**
 * Base plugin class
 *
 * @author Stefan Meyer <meyer@leifos.com>
 */
class ilApacheAuthPlugin extends ilAuthPlugin implements ilAuthDefinition
{
    /**
     * @var ilApacheAuthPlugin|null
     */
    private static $instance = null;

    const CTYPE = 'Services';
    const CNAME = 'Authentication';
    const SLOT_ID = 'authhk';
    const PNAME = 'ApacheAuth';
    
    const AUTH_ID_BASE = 2000;

    const AUTH_NAME = 'Netscaler SSO';
    
    
    /**
     * Get singleton instance
     * @global ilPluginAdmin $ilPluginAdmin
     * @return ilApacheAuthPlugin
     */
    public static function getInstance() : ilApacheAuthPlugin
    {
        if (self::$instance) {
            return self::$instance;
        }

        include_once './Services/Component/classes/class.ilPluginAdmin.php';
        return self::$instance = ilPluginAdmin::getPluginObject(
            self::CTYPE,
            self::CNAME,
            self::SLOT_ID,
            self::PNAME
        );
    }

    /**
     * Init autoloading
     */
    protected function init()
    {
        $this->initAutoLoad();
    }
    
    /**
     * Get name of plugin.
     */
    public function getPluginName()
    {
        return self::PNAME;
    }


    /**
     * Get all active auth ids
     * @return int[]
     */
    public function getAuthIds() : array
    {
        return [
            self::AUTH_ID_BASE
        ];
    }

    /**
     * Get auth id by name
     * @param string $a_auth_name
     */
    public function getAuthIdByName($a_auth_name) : int
    {
        return self::AUTH_ID_BASE;
    }
    
    /**
     * Get auth name by id
     * @param int $a_auth_id
     * @return string
     */
    public function getAuthName($a_auth_id) : string
    {
        return self::AUTH_NAME;
    }


    /**
     *
     * @param string $a_auth_id
     * @return int
     */
    public function getLocalPasswordValidationType($a_auth_id) : int
    {
        return ilAuthUtils::LOCAL_PWV_FULL;
    }

    /**
     * @inheritDoc
     */
    public function isExternalAccountNameRequired($a_auth_id) : bool
    {
        return true;
    }

    /**
     * Check if password modification is allowed
     *
     * @param int $a_auth_id
     */
    public function isPasswordModificationAllowed($a_auth_id) : bool
    {
        return false;
    }

    /**
     * Check multiple auth tries are suported
     * @param int $a_auth_id
     * @return boolean
     */
    public function supportsMultiCheck($a_auth_id) : bool
    {
        return true;
    }

    /**
     * Get options for mutliple auth mode selection
     * @param int $a_auth_id
     */
    public function getMultipleAuthModeOptions($a_auth_id) : array
    {
        return array();
    }
    
    
    
    /**
     * Extract auth id
     * @param int $a_auth_id
     * @return int
     */
    protected function extractServerId($a_auth_id) : int
    {
        return 0;
    }

    /**
     * Check if auth is active
     * @param int $a_auth_id
     */
    public function isAuthActive($a_auth_id) : bool
    {
        return true;
    }
    
    /**
     * Init auto loader
     * @return void
     */
    protected function initAutoLoad()
    {
        global $DIC;
        $logger = $DIC->logger()->auth();
        $logger->debug('Init auto load');
        spl_autoload_register(
            array($this,'autoLoad')
        );
    }

    /**
     * Auto load implementation
     *
     * @param string class name
     * @return void
     */
    private function autoLoad(string $a_classname) : void
    {
        $class_file = $this->getClassesDirectory() . '/class.' . $a_classname . '.php';
        if (file_exists($class_file) && include_once($class_file)) {
            return;
        }
    }

    /**
     * @inheritDoc
     */
    public function getProvider(ilAuthCredentials $credentials, $a_auth_mode) : ilAuthProviderApacheAuth
    {
        return new ilAuthProviderApacheAuth($credentials);
    }
}
