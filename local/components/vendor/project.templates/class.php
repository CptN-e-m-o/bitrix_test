<?php

use Bitrix\Main\Engine\Contract\Controllerable;
if (!\Bitrix\Main\Loader::includeModule('vendor.projecttemplates')) {
    throw new \Exception('Модуль ProjectTemplates не подключен');
}

use Vendor\ProjectTemplates\Service\TemplateDeployer;
use Vendor\ProjectTemplates\Table\ProjectTemplateTable;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\UserTable;
use Vendor\ProjectTemplates\Table\ProjectTemplateTaskTable;

class ProjectTemplatesComponent extends CBitrixComponent implements Controllerable
{
    public function executeComponent()
    {
        $this->arResult['TEMPLATES'] = ProjectTemplateTable::getList()->fetchAll();

        if (empty($this->arResult['TEMPLATES'])) {
            $this->arResult['TEMPLATES'] = [
                ['ID' => 1, 'NAME' => 'Тестовый шаблон']
            ];
        }

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

    public function getTemplateInfoAction(int $templateId): array
    {
        $template = ProjectTemplateTable::getById($templateId)->fetch();

        if (!$template) {
            throw new \Bitrix\Main\SystemException('Template not found');
        }

        $templateUser = UserTable::getById($template['RESPONSIBLE_ID'])->fetch();

        $tasksRaw = ProjectTemplateTaskTable::getList([
            'select' => [
                'TITLE',
                'DESCRIPTION',
                'RESPONSIBLE_ID',
                'DEADLINE_OFFSET_DAYS',
            ],
            'filter' => [
                'TEMPLATE_ID' => $templateId,
            ],
        ])->fetchAll();

        $responsibleIds = array_unique(
            array_column($tasksRaw, 'RESPONSIBLE_ID')
        );

        $users = [];

        if ($responsibleIds) {
            $userRows = UserTable::getList([
                'select' => ['ID', 'NAME', 'LAST_NAME'],
                'filter' => ['ID' => $responsibleIds],
            ])->fetchAll();

            foreach ($userRows as $user) {
                $users[$user['ID']] = trim($user['NAME'] . ' ' . $user['LAST_NAME']);
            }
        }

        $now = new \Bitrix\Main\Type\DateTime();
        $tasks = [];

        foreach ($tasksRaw as $task) {
            $deadline = clone $now;
            $deadline->add('+' . (int)$task['DEADLINE_OFFSET_DAYS'] . ' days');

            $tasks[] = [
                'title' => $task['TITLE'],
                'description' => $task['DESCRIPTION'],
                'responsible' => $users[$task['RESPONSIBLE_ID']] ?? 'Не указан',
                'deadline' => $deadline->format('d.m.Y'),
            ];
        }

        return [
            'name' => $template['NAME'],
            'responsible' => $templateUser
                ? trim($templateUser['NAME'] . ' ' . $templateUser['LAST_NAME'])
                : 'Не указан',
            'tasks' => $tasks,
        ];
    }
}
