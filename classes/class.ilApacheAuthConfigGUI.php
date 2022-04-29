<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 *********************************************************************/

/**
 * Description of class
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 */
class ilApacheAuthConfigGUI extends ilPluginConfigGUI
{

    /**
     * @var ilCtrl
     */
    private $ctrl;

    /**
     * @var ilLanguage
     */
    private $lng;

    /**
     * @var ilTabsGUI
     */
    private $tabs;

    /**
     * @var ilTemplate
     */
    private $tpl;

    public function __construct()
    {
        global $DIC;

        $this->ctrl = $DIC->ctrl();
        $this->lng = $DIC->language();
        $this->tabs = $DIC->tabs();
        $this->tpl = $DIC->ui()->mainTemplate();
    }

    /**
     * Handles all commmands, default is "configure"
     */
    public function performCommand($cmd)
    {

        $this->ctrl->saveParameter($this, "menu_id");

        switch ($cmd) {
            default:
                $this->$cmd();
                break;

        }
    }

    /**
     * Show settings screen
     */
    protected function configure(ilPropertyFormGUI $form = null)
    {
        $this->tabs->activateTab('settings');

        $this->tabs->addTab(
            'settings',
            ilApacheAuthPlugin::getInstance()->txt('tab_settings'),
            $GLOBALS['ilCtrl']->getLinkTarget($this, 'configure')
        );

        if (!$form instanceof ilPropertyFormGUI) {
            $form = $this->initConfigurationForm();
        }
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * Init configuration form
     * @return ilPropertyFormGUI
     */
    protected function initConfigurationForm() : ilPropertyFormGUI
    {
        $settings = ilApacheAuthPluginSettings::getInstance();

        $form = new ilPropertyFormGUI();
        $form->setTitle($this->getPluginObject()->txt('tbl_auth_settings'));
        $form->setFormAction($this->ctrl->getFormAction($this));
        $form->addCommandButton('save', $this->lng->txt('save'));
        $form->setShowTopButtons(false);

        $indicator_name = new ilTextInputGUI($this->getPluginObject()->txt('apache_indicator_name'), 'name');
        $indicator_name->setValue($settings->getIndicatorName());
        $indicator_name->setRequired(true);

        $form->addItem($indicator_name);

        $indicator_value = new ilTextInputGUI($this->getPluginObject()->txt('apache_indicator_value'), 'value');
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
        $form = $this->initConfigurationForm();
        $settings = ilApacheAuthPluginSettings::getInstance();

        if ($form->checkInput()) {
            $settings->setIndicatorName($form->getInput('name'));
            $settings->setIndicatorValue($form->getInput('value'));
            $settings->setUsernameField($form->getInput('uname'));
            $settings->save();

            ilUtil::sendSuccess($this->lng->txt('settings_saved'), true);
            $this->ctrl->redirect($this, 'configure');
        }

        $error = $this->lng->txt('err_check_input');
        $form->setValuesByPost();
        ilUtil::sendFailure($error);
        $this->configure($form);
    }
}
