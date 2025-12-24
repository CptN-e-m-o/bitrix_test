<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Vendor\ProjectTemplates\Table\ProjectTemplateTable;

Loc::loadMessages(__FILE__);

Loader::includeModule('vendor.projecttemplates');

$APPLICATION->SetTitle(Loc::getMessage('VENDOR_PT_LIST_TITLE'));

$tableId = 'vendor_projecttemplates_list';
$list = new CAdminList($tableId);

$list->CheckListMode();

if (
    $_REQUEST['action'] === 'delete'
    && (int)$_REQUEST['ID'] > 0
) {
    ProjectTemplateTable::delete((int)$_REQUEST['ID']);

    LocalRedirect($APPLICATION->GetCurPageParam('', ['action', 'ID']));
}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

$rsData = ProjectTemplateTable::getList([
    'order' => ['ID' => 'DESC'],
]);

$list->AddHeaders([
    ['id' => 'ID', 'content' => Loc::getMessage('VENDOR_PT_LIST_ID'), 'sort' => 'ID', 'default' => true],
    ['id' => 'NAME', 'content' => Loc::getMessage('VENDOR_PT_LIST_NAME'), 'default' => true],
    ['id' => 'RESPONSIBLE_ID', 'content' => Loc::getMessage('VENDOR_PT_LIST_RESPONSIBLE'), 'default' => true],
    ['id' => 'CREATED_AT', 'content' => Loc::getMessage('VENDOR_PT_LIST_CREATED_AT'), 'default' => true],
]);

while ($row = $rsData->fetch()) {
    $item = $list->AddRow($row['ID'], $row);

    $item->AddViewField(
        'NAME',
        sprintf(
            '<a href="projecttemplates_edit.php?ID=%d&lang=%s">%s</a>',
            $row['ID'],
            LANGUAGE_ID,
            htmlspecialcharsbx($row['NAME'])
        )
    );

    $item->AddActions([
        [
            'ICON' => 'edit',
            'TEXT' => Loc::getMessage('VENDOR_PT_LIST_EDIT'),
            'ACTION' => $list->ActionRedirect(
                'projecttemplates_edit.php?ID=' . $row['ID'] . '&lang=' . LANGUAGE_ID
            ),
        ],
        [
            'ICON' => 'delete',
            'TEXT' => Loc::getMessage('VENDOR_PT_LIST_DELETE'),
            'ACTION' =>
                "if(confirm('".Loc::getMessage('VENDOR_PT_LIST_DELETE_CONFIRM')."')) window.location='"
                . $APPLICATION->GetCurPageParam(
                    'action=delete&ID=' . $row['ID'],
                    ['action', 'ID']
                )
                . "';",
        ],
    ]);
}

$list->AddAdminContextMenu([
    [
        'TEXT' => Loc::getMessage('VENDOR_PT_LIST_ADD_TEMPLATE'),
        'LINK' => 'projecttemplates_edit.php?lang=' . LANGUAGE_ID,
        'ICON' => 'btn_new',
    ],
]);

$list->DisplayList();

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
