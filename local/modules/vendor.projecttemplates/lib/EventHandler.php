<?php

class EventHandler
{
    public static function onBuildGlobalMenu(&$globalMenu, &$moduleMenu)
    {
        $moduleMenu[] = [
            'parent_menu' => 'global_menu_tasks',
            'text' => 'Шаблоны проектов',
            'url' => '/project-templates/',
            'icon' => 'tasks_menu_icon',
        ];
    }
}
