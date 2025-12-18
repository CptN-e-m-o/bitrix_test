<?php

namespace Vendor\ProjectTemplates\Service;

use Bitrix\Main\Type\DateTime;
use Bitrix\Tasks\Internals\TaskTable;
use Vendor\ProjectTemplates\Table\ProjectTemplateTable;
use Vendor\ProjectTemplates\Table\ProjectTemplateTaskTable;

class TemplateDeployer
{
    public function deploy(int $templateId): int
    {
        $template = ProjectTemplateTable::getById($templateId)->fetch();
        if (!$template) {
            throw new \RuntimeException('Template not found');
        }

        $projectId = $this->createProject($template);

        $tasks = ProjectTemplateTaskTable::getList([
            'filter' => ['TEMPLATE_ID' => $templateId],
            'order' => ['ID' => 'ASC'],
        ])->fetchAll();

        foreach ($tasks as $task) {
            $this->createTask($task, $projectId);
        }

        return $projectId;
    }

    private function createProject(array $template): int
    {
        $result = \Bitrix\Tasks\Internals\ProjectTable::add([
            'NAME' => $template['NAME'] . ' - ' . date('d.m.Y'),
            'CREATED_BY' => $template['RESPONSIBLE_ID'],
            'OWNER_ID' => $template['RESPONSIBLE_ID'],
        ]);

        if (!$result->isSuccess()) {
            throw new \RuntimeException(implode(', ', $result->getErrorMessages()));
        }

        return $result->getId();
    }

    private function createTask(array $task, int $projectId): void
    {
        $deadline = new DateTime();
        $deadline->add('+' . $task['DEADLINE_OFFSET_DAYS'] . ' days');

        $result = TaskTable::add([
            'TITLE' => $task['TITLE'],
            'DESCRIPTION' => $task['DESCRIPTION'],
            'RESPONSIBLE_ID' => $task['RESPONSIBLE_ID'],
            'GROUP_ID' => $projectId,
            'DEADLINE' => $deadline,
        ]);

        if (!$result->isSuccess()) {
            throw new \RuntimeException(implode(', ', $result->getErrorMessages()));
        }
    }
}
