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
	private static $instance = null;

	const CTYPE = 'Services';
	const CNAME = 'Authentication';
	const SLOT_ID = 'authhk';
	const PNAME = 'ApacheAuth';
	
	const AUTH_ID_BASE = 2000;
	
	
	/**
	 * Get singleton instance
	 * @global ilPluginAdmin $ilPluginAdmin
	 * @return ilApacheAuthPlugin
	 */
	public static function getInstance()
	{
		if(self::$instance)
		{
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
	 * Get name of plugin.
	 */
	public function getPluginName()
	{
		return self::PNAME;
	}

	/**
	 * Init slot
	 */
	protected function slotInit()
	{
		$this->initAutoLoad();
	}

	/**
	 * Get all active auth ids
	 * @return array int
	 */
	public function getAuthIds()
	{
		return array();
	}

	/**
	 * Get auth id by name
	 * @param type $a_auth_name
	 */
	public function getAuthIdByName($a_auth_name)
	{
		if(stristr($a_auth_name, '_'))
		{
			$exploded = explode('_',$a_auth_name);
			return self::AUTH_ID_BASE + $exploded[1];
		}
		return self::AUTH_ID_BASE;
	}
	
	/**
	 * Get auth name by id
	 * @param type $a_auth_id
	 * @return string
	 */
	public function getAuthName($a_auth_id)
	{
		$GLOBALS['ilLog']->getLogger()->warning('Called get auth name for apache plugin');
		$GLOBALS['ilLog']->getLogger()->logStack(ilLogLevel::WARNING);
		return '';
	}

	
	/**
	 * Get container
	 */
	public function getContainer($a_auth_id)
	{
		$this->includeClass('class.ilAuthContainerApachePlugin.php');
		$container = new ilAuthContainerApachePlugin();
		return $container;
	}

	/**
	 * 
	 * @param type $a_auth_id
	 * @return type
	 */
	public function getLocalPasswordValidationType($a_auth_id)
	{
		return ilAuthUtils::LOCAL_PWV_FULL;
	}

	/**
	 * 
	 */
	public function isExternalAccountNameRequired($a_auth_id)
	{
		return true;
	}

	/**
	 * Check if password modification is allowed
	 */
	public function isPasswordModificationAllowed($a_auth_id)
	{
		return false;
	}

	/**
	 * Check multiple auth tries are suported
	 * @param type $a_auth_id
	 * @return boolean
	 */
	public function supportsMultiCheck($a_auth_id)
	{
		return true;
	}

	/**
	 * Get options for mutliple auth mode selection
	 * @param type $a_auth_id
	 */
	public function getMultipleAuthModeOptions($a_auth_id)
	{
		return array();
	}
	
	
	
	/**
	 * Extract auth id
	 * @param type $a_auth_id
	 * @return int 
	 */
	protected function extractServerId($a_auth_id)
	{
		return 0;
	}

	/**
	 * Check if auth is active
	 * @param type $a_auth_id
	 */
	public function isAuthActive($a_auth_id)
	{
		return true;
	}
	
	/**
	 * Init auto loader
	 * @return void
	 */
	protected function initAutoLoad()
	{
		$GLOBALS['ilLog']->getLogger('lfskyauth')->debug('Init auto load');
		spl_autoload_register(
			array($this,'autoLoad')
		);
	}

	/**
	 * Auto load implementation
	 *
	 * @param string class name
	 */
	private final function autoLoad($a_classname)
	{
		$class_file = $this->getClassesDirectory().'/class.'.$a_classname.'.php';
		@include_once($class_file);
	}
}
?>