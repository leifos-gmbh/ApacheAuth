<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 *  Authentication settings
 *
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 */
class ilApacheAuthPluginSettings
{
    /**
     * @var null|ilApacheAuthPluginSettings
     */
    private static $instance = null;

    /**
     * @var ilSetting|null
     */
    private $storage = null;

    /**
     * @var string
     */
    private $name = 'AUTH_TYPE';
    /**
     * @var string
     */
    private $value = 'Basic';
    /**
     * @var string
     */
    private $uname = 'REMOTE_USER';

    /**
     * Singelton constructor
     */
    protected function __construct()
    {
        $this->storage = new ilSetting('lfskyauth_settings');
        $this->read();
    }
    
    /**
     * Get Instance
     * @return ilApacheAuthPluginSettings
     */
    public static function getInstance() : ilApacheAuthPluginSettings
    {
        if (self::$instance) {
            return self::$instance;
        }
        return self::$instance = new self();
    }
    
    /**
     * Get storage
     * @return ilSetting
     */
    protected function getStorage() : ilSetting
    {
        return $this->storage;
    }

    /**
     * @param string $a_name
     */
    public function setIndicatorName(string $a_name)
    {
        $this->name = $a_name;
    }

    /**
     * @return string
     */
    public function getIndicatorName() : string
    {
        return $this->name;
    }

    /**
     * @param string $a_value
     */
    public function setIndicatorValue(string $a_value)
    {
        $this->value = $a_value;
    }

    /**
     * @return string
     */
    public function getIndicatorValue() : string
    {
        return $this->value;
    }

    /**
     * @param string $a_uname
     */
    public function setUsernameField(string $a_uname)
    {
        $this->uname = $a_uname;
    }

    /**
     * @return string
     */
    public function getUsernameField() : string
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
