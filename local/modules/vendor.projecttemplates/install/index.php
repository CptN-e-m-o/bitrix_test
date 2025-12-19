<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Vendor\ProjectTemplates\Table\ProjectTemplateTable;

Loc::loadMessages(__FILE__);

class vendor_projecttemplates extends CModule
{
    public $MODULE_ID = 'vendor.projecttemplates';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . '/../.version.php');

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

        $this->MODULE_NAME = 'Project Templates';
        $this->MODULE_DESCRIPTION = 'Модуль шаблонов проектов';
    }

    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installDB();
    }

    public function DoUninstall()
    {
        $this->uninstallDB();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function installDB()
    {
        require_once __DIR__ . '/../include.php';

        $connection = Application::getConnection();

        if (!$connection->isTableExists(ProjectTemplateTable::getTableName())) {
            ProjectTemplateTable::getEntity()->createDbTable();
        }

        ProjectTemplateTable::add([
            'NAME' => 'Тестовый шаблон проекта 1',
            'RESPONSIBLE_ID' => 1,
            'CREATED_AT' => new \Bitrix\Main\Type\DateTime(),
        ]);

        ProjectTemplateTable::add([
            'NAME' => 'Тестовый шаблон проекта 2',
            'RESPONSIBLE_ID' => 1,
            'CREATED_AT' => new \Bitrix\Main\Type\DateTime(),
        ]);
    }

    public function uninstallDB()
    {
        require_once __DIR__ . '/../include.php';

        $connection = Application::getConnection();

        if ($connection->isTableExists('vendor_project_templates')) {
            $connection->queryExecute(
                'DROP TABLE ' . ProjectTemplateTable::getTableName()
            );
        }
    }
}
