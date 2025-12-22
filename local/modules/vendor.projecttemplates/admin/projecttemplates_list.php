<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

use Bitrix\Main\Loader;
use Vendor\ProjectTemplates\Table\ProjectTemplateTable;

Loader::includeModule('vendor.projecttemplates');

$APPLICATION->SetTitle('Шаблоны проектов');

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

$tableId = 'vendor_projecttemplates_list';
$list = new CAdminList($tableId);

$rsData = ProjectTemplateTable::getList([
    'order' => ['ID' => 'DESC'],
]);

$list->AddHeaders([
    ['id' => 'ID', 'content' => 'ID', 'sort' => 'ID', 'default' => true],
    ['id' => 'NAME', 'content' => 'Название', 'default' => true],
    ['id' => 'RESPONSIBLE_ID', 'content' => 'Ответственный', 'default' => true],
    ['id' => 'CREATED_AT', 'content' => 'Создан', 'default' => true],
]);

while ($row = $rsData->fetch()) {
    $item = $list->AddRow($row['ID'], $row);

    $item->AddViewField('NAME', sprintf(
        '<a href="projecttemplates_edit.php?ID=%d&lang=%s">%s</a>',
        $row['ID'],
        LANGUAGE_ID,
        htmlspecialcharsbx($row['NAME'])
    ));

    $item->AddActions([
        [
            'ICON' => 'edit',
            'TEXT' => 'Редактировать',
            'ACTION' => $list->ActionRedirect(
                'projecttemplates_edit.php?ID=' . $row['ID'] . '&lang=' . LANGUAGE_ID
            ),
        ],
        [
            'ICON' => 'delete',
            'TEXT' => 'Удалить',
            'ACTION' => "if(confirm('Удалить?')) " . $list->ActionDoGroup(
                    $row['ID'],
                    'delete'
                ),
        ],
    ]);
}

if ($ids = $list->GroupAction()) {
    foreach ($ids as $id) {
        ProjectTemplateTable::delete((int)$id);
    }
}

$list->AddAdminContextMenu([
    [
        'TEXT' => 'Добавить шаблон',
        'LINK' => 'projecttemplates_edit.php?lang=' . LANGUAGE_ID,
        'ICON' => 'btn_new',
    ],
]);

$list->DisplayList();

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
