<?php

use Bitrix\Main\Localization\Loc;

class company_project_templates extends CModule
{
    public $MODULE_ID = 'company.project_templates';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('MY_MODULE_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('MY_MODULE_MODULE_DESCRIPTION');
    }
    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);
        return true;
    }
    public function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);
        return true;
    }
}