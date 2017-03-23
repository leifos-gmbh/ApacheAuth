<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once './Services/Component/classes/class.ilPluginConfigGUI.php';

/**
 * Description of class
 *
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 */
class ilApacheAuthConfigGUI extends ilPluginConfigGUI
{
	/**
	* Handles all commmands, default is "configure"
	*/
	public function performCommand($cmd)
	{
		global $ilCtrl;
		global $ilTabs;
		
		$ilCtrl->saveParameter($this, "menu_id");
		
		switch ($cmd)
		{
			default:
				$this->$cmd();
				break;

		}
	}
	
	/**
	 * Show settings screen
	 * @global type $tpl
	 * @global type $ilTabs 
	 */
	protected function configure(ilPropertyFormGUI $form = null)
	{
		global $tpl, $ilTabs;

		$ilTabs->activateTab('settings');
		
		$ilTabs->addTab(
			'settings',
			ilApacheAuthPlugin::getInstance()->txt('tab_settings'),
			$GLOBALS['ilCtrl']->getLinkTarget($this,'configure')
		);
		

		if(!$form instanceof ilPropertyFormGUI)
		{
			$form = $this->initConfigurationForm();
		}
		$tpl->setContent($form->getHTML());
	}
	
	/**
	 * Init configuration form
	 * @global type $ilCtrl 
	 */
	protected function initConfigurationForm()
	{
		global $ilCtrl, $lng;
		
		$settings = ilApacheAuthPluginSettings::getInstance();
		
		include_once './Services/Form/classes/class.ilPropertyFormGUI.php';
		
		$form = new ilPropertyFormGUI();
		$form->setTitle($this->getPluginObject()->txt('tbl_auth_settings'));
		$form->setFormAction($ilCtrl->getFormAction($this));
		$form->addCommandButton('save', $lng->txt('save'));
		$form->setShowTopButtons(false);
		
		$indicator_name = new ilTextInputGUI($this->getPluginObject()->txt('apache_indicator_name'),'name');
		$indicator_name->setValue($settings->getIndicatorName());
		$indicator_name->setRequired(true);
		
		$form->addItem($indicator_name);
		
		$indicator_value = new ilTextInputGUI($this->getPluginObject()->txt('apache_indicator_value'),'value');
		$indicator_value->setValue($settings->getIndicatorValue());
		$indicator_value->setRequired(true);
		
		$form->addItem($indicator_value);
		
		$uname_field = new ilTextInputGUI($this->getPluginObject()->txt('apache_uname_field'), 'uname');
		$uname_field->setValue($settings->getUsernameField());
		$uname_field->setRequired(true);
		
		$form->addItem($uname_field);
		
		return $form;
	}
	
	/**
	 * Save settings
	 */
	protected function save()
	{
		global $lng, $ilCtrl;
		
		$form = $this->initConfigurationForm();
		$settings = ilApacheAuthPluginSettings::getInstance();
		
		if($form->checkInput())
		{
			$settings->setIndicatorName($form->getInput('name'));
			$settings->setIndicatorValue($form->getInput('value'));
			$settings->setUsernameField($form->getInput('uname'));
			$settings->save();
				
			ilUtil::sendSuccess($lng->txt('settings_saved'),true);
			$ilCtrl->redirect($this,'configure');
		}
		
		$error = $lng->txt('err_check_input');
		$form->setValuesByPost();
		ilUtil::sendFailure($error);
		$this->configure($form);
	}
	
}
?>