<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Vendor\ProjectTemplates\Table\ProjectTemplateTable;
use Vendor\ProjectTemplates\Table\ProjectTemplateTaskTable;

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
        CopyDirFiles(
            __DIR__ . "/../admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin",
            true,
            true
        );

        $this->installDB();
    }

    public function DoUninstall()
    {
        $this->uninstallDB();

        DeleteDirFiles(__DIR__ . "/../admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function installDB()
    {
        require_once __DIR__ . '/../include.php';

        $connection = Application::getConnection();

        if (!$connection->isTableExists(ProjectTemplateTable::getTableName())) {
            ProjectTemplateTable::getEntity()->createDbTable();
        }

        if (!$connection->isTableExists(ProjectTemplateTaskTable::getTableName())) {
            ProjectTemplateTaskTable::getEntity()->createDbTable();
        }

        $template1 = ProjectTemplateTable::add([
            'NAME' => 'Тестовый шаблон проекта 1',
            'RESPONSIBLE_ID' => 1,
            'CREATED_AT' => new \Bitrix\Main\Type\DateTime(),
        ]);

        $template2 = ProjectTemplateTable::add([
            'NAME' => 'Тестовый шаблон проекта 2',
            'RESPONSIBLE_ID' => 1,
            'CREATED_AT' => new \Bitrix\Main\Type\DateTime(),
        ]);

        $template1Id = $template1->getId();
        $template2Id = $template2->getId();

        ProjectTemplateTaskTable::add([
            'TEMPLATE_ID' => $template1Id,
            'TITLE' => 'Первая тестовая задача',
            'DESCRIPTION' => 'Описание первой задачи',
            'RESPONSIBLE_ID' => 1,
            'DEADLINE_OFFSET_DAYS' => 1,
        ]);

        ProjectTemplateTaskTable::add([
            'TEMPLATE_ID' => $template1Id,
            'TITLE' => 'Вторая тестовая задача',
            'DESCRIPTION' => 'Описание второй задачи',
            'RESPONSIBLE_ID' => 1,
            'DEADLINE_OFFSET_DAYS' => 3,
        ]);

        ProjectTemplateTaskTable::add([
            'TEMPLATE_ID' => $template2Id,
            'TITLE' => 'Задача для шаблона 2',
            'DESCRIPTION' => 'Описание задачи',
            'RESPONSIBLE_ID' => 1,
            'DEADLINE_OFFSET_DAYS' => 2,
        ]);
    }

    public function uninstallDB()
    {
        require_once __DIR__ . '/../include.php';

        $connection = Application::getConnection();

        if ($connection->isTableExists(ProjectTemplateTaskTable::getTableName())) {
            $connection->queryExecute(
                'DROP TABLE ' . ProjectTemplateTaskTable::getTableName()
            );
        }

        if ($connection->isTableExists(ProjectTemplateTable::getTableName())) {
            $connection->queryExecute(
                'DROP TABLE ' . ProjectTemplateTable::getTableName()
            );
        }
    }

}
