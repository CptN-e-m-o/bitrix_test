<?php

namespace Vendor\ProjectTemplates\Service;

use Bitrix\Main\Type\DateTime;
use Bitrix\Tasks\Internals\TaskTable;
use CMain;
use CModule;
use CSocNetGroup;
use Vendor\ProjectTemplates\Table\ProjectTemplateTable;
use Vendor\ProjectTemplates\Table\ProjectTemplateTaskTable;

class TemplateDeployer
{
    public function deploy(int $templateId): array
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

        return [
            'success' => true,
            'projectId' => $projectId,
        ];
    }


    private function createProject(array $template): int
    {
        if (!\Bitrix\Main\Loader::includeModule('socialnetwork')) {
            throw new \Exception('Модуль socialnetwork не подключен');
        }

        if (empty($template['NAME'])) {
            throw new \Exception('Не указано название проекта в шаблоне');
        }
        if (empty($template['RESPONSIBLE_ID'])) {
            throw new \Exception('Не указан ответственный в шаблоне');
        }

        // Подключаем глобальный $APPLICATION, если его нет (важно для AJAX/компонентов)
        global $APPLICATION;

        // Обязательные поля
        $groupFields = [
            'SITE_ID'       => 's1',  // Или SITE_ID, если определена константа. Проверьте в /bitrix/admin/site_edit.php
            'NAME'          => $template['NAME'] . ' #' . $template['ID'],
            'DESCRIPTION'   => 'Проект создан по шаблону ID ' . $template['ID'],
            'VISIBLE'       => 'Y',
            'OPENED'        => 'Y',
            'PROJECT'       => 'Y',  // Делает группу проектом
            'SUBJECT_ID'    => 1,    // !!! Критично! ID темы группы. Проверьте в админке: Сервисы > Социальная сеть > Темы групп


            'INITIATE_PERMS' => 'E',  // Кто может приглашать новых участников:
            // 'A' — владелец
            // 'E' — модераторы группы
            // 'K' — все участники группы (рекомендую для открытых проектов)
            // 'M' — владелец и модераторы
            'SPAM_PERMS'     => 'K',
        ];

        // ID владельца (он же будет добавлен как владелец группы)
        $ownerId = (int)$template['RESPONSIBLE_ID'];

        // Создаём группу
        $groupId = CSocNetGroup::CreateGroup($ownerId, $groupFields, false); // Третий параметр — не индексировать сразу (опционально)

        if (!$groupId) {
            $errorMessage = 'Неизвестная ошибка создания группы';

            // Пытаемся получить ошибку через $APPLICATION
            if (isset($APPLICATION) && $APPLICATION instanceof CMain) {
                if ($ex = $APPLICATION->GetException()) {
                    $errorMessage = $ex->GetString();
                    $APPLICATION->ResetException(); // Сбрасываем, чтобы не накапливалось
                }
            }

            throw new \Exception('Ошибка создания проекта: ' . $errorMessage);
        }

        return (int)$groupId;
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
