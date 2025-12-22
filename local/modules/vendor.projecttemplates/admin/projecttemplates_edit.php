<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Vendor\ProjectTemplates\Table\ProjectTemplateTable;

Loader::includeModule('vendor.projecttemplates');

global $APPLICATION;

$APPLICATION->SetTitle('Редактирование шаблона проекта');

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';

$ID = isset($_GET['ID']) ? (int)$_GET['ID'] : 0;
$errors = [];

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_bitrix_sessid() && isset($_POST['save'])) {
    $fields = [
        'NAME' => $_POST['NAME'] ?? '',
        'RESPONSIBLE_ID' => $_POST['RESPONSIBLE_ID'] ?? '',
    ];

    if ($ID > 0) {
        $result = ProjectTemplateTable::update($ID, $fields);
    } else {
        $result = ProjectTemplateTable::add($fields);
        if ($result->isSuccess()) {
            $ID = $result->getId();
        }
    }

    if (!$result->isSuccess()) {
        $errors = $result->getErrorMessages();
    } else {
        LocalRedirect("/bitrix/admin/projecttemplates_list.php");
    }
}

// Получение данных для редактирования
$arData = [];
if ($ID > 0) {
    $arData = ProjectTemplateTable::getById($ID)->fetch();
}

$aTabs = [
    [
        "DIV" => "edit1",
        "TAB" => "Основные",
        "TITLE" => "Основные настройки шаблона"
    ],
];

$arUsersOptions = [];
$rsUsers = CUser::GetList(
    ($by = "last_name"),
    ($order = "asc"),
    [
        'ACTIVE' => 'Y',
    ],
    ["FIELDS" => ["ID", "NAME", "LAST_NAME", "LOGIN"]]
);

while ($user = $rsUsers->Fetch()) {
    $arUsersOptions[$user['ID']] = htmlspecialcharsbx($user['LAST_NAME'].' '.$user['NAME']);
}

$tabControl = new CAdminForm("template_form", $aTabs);

echo '<form method="POST" action="">';
echo bitrix_sessid_post();

$tabControl->Begin();
$tabControl->BeginNextFormTab();

if ($ID > 0) {
    $tabControl->AddViewField("ID", "ID", $ID);
    $tabControl->AddViewField("CREATED_AT", "Дата создания", $arData['CREATED_AT']);
}

$tabControl->AddEditField("NAME", "Название шаблона", false, ["size" => 50], $arData['NAME'] ?? '');
$tabControl->AddDropDownField(
    "RESPONSIBLE_ID",
    "Ответственный",
    false,
    $arUsersOptions,
    $arData['RESPONSIBLE_ID'] ?? ''
);

$tabControl->Buttons([
    "btnSave" => true,
    "btnCancel" => true
]);

$tabControl->Show();

echo '</form>';

if (!empty($errors)) {
    CAdminMessage::ShowMessage([
        "TYPE" => "ERROR",
        "MESSAGE" => "Ошибка сохранения",
        "DETAILS" => implode("<br>", $errors),
        "HTML" => true
    ]);
}

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
