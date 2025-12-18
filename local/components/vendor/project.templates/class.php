<?php

use Bitrix\Main\Engine\Contract\Controllerable;
if (!\Bitrix\Main\Loader::includeModule('vendor.projecttemplates')) {
    throw new \Exception('Модуль ProjectTemplates не подключен');
}

use Vendor\ProjectTemplates\Service\TemplateDeployer;
use Vendor\ProjectTemplates\Table\ProjectTemplateTable;

class ProjectTemplatesComponent extends CBitrixComponent implements Controllerable
{
    public function executeComponent()
    {
        $this->arResult['TEMPLATES'] = ProjectTemplateTable::getList()->fetchAll();
        $this->includeComponentTemplate();
    }

    public function configureActions()
    {
        return [
            'deploy' => ['prefilters' => []],
        ];
    }

    public function deployAction(int $templateId)
    {
        $service = new TemplateDeployer();
        return $service->deploy($templateId);
    }
}
