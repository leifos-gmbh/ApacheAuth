<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Fhoev event settings
 *
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 */
class ilApacheAuthPluginSettings
{
	private static $instance = null;
	
	private $storage = null;
	
	private $name = 'AUTH_TYPE';
	private $value = 'Basic';
	private $uname = 'REMOTE_USER';

	/**
	 * Singelton constructor
	 */
	protected function __construct()
	{
		include_once './Services/Administration/classes/class.ilSetting.php';
		$this->storage = new ilSetting('lfskyauth_settings');
		$this->read();
	}
	
	/**
	 * Get Instance
	 * @return ilApacheAuthPluginSettings
	 */
	public static function getInstance()
	{
		if(self::$instance)
		{
			return self::$instance;
		}
		return self::$instance = new self();
	}
	
	/**
	 * Get storage
	 * @return ilSetting
	 */
	protected function getStorage()
	{
		return $this->storage;
	}

	public function setIndicatorName($a_name)
	{
		$this->name = $a_name;
	}
	
	public function getIndicatorName()
	{
		return $this->name;
	}
	
	public function setIndicatorValue($a_value)
	{
		$this->value = $a_value;
	}
	
	public function getIndicatorValue()
	{
		return $this->value;
	}
	
	public function setUsernameField($a_uname)
	{
		$this->uname = $a_uname;
	}
	
	public function getUsernameField()
	{
		return $this->uname;
	}
	
	/**
	 * Save settings
	 */
	public function save()
	{
		$this->getStorage()->set('name', $this->getIndicatorName());
		$this->getStorage()->set('value', $this->getIndicatorValue());
		$this->getStorage()->set('uname', $this->getUsernameField());
	}
	
	/**
	 * Read settings
	 */
	public function read()
	{
		$this->setIndicatorName($this->getStorage()->get('name', $this->getIndicatorName()));
		$this->setIndicatorValue($this->getStorage()->get('value', $this->getIndicatorValue()));
		$this->setUsernameField($this->getStorage()->get('uname', $this->getUsernameField()));
	}
}
?>