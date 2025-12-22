<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

use Bitrix\Main\UI\Extension;
use Bitrix\Main\UI;
use Bitrix\UI\Buttons;

if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm("Доступ запрещен");
    return;
}

$APPLICATION->SetTitle("Настройки шаблонов проектов");

$arTabs = array(
    array(
        "DIV" => "edit1",
        "TAB" => "Основные настройки",
        "TITLE" => "Основные параметры модуля"
    )
);

$tabControl = new CAdminTabControl("tabControl", $arTabs);

$form = new CAdminForm("vendorProjectTemplatesForm", $arTabs);
$form->Begin(array(
    "FORM_ACTION" => $APPLICATION->GetCurPage()
));

$form->BeginNextFormTab();

$form->AddSection("SECTION_GENERAL", "Общие настройки");

$message = new CAdminMessage(array(
    "MESSAGE" => "Информация",
    "DETAILS" => "Это пустая панель настроек. Добавьте параметры позже.",
    "HTML" => true,
    "TYPE" => "OK"
));
echo $message->Show();

Extension::load("ui.buttons");
?>

<button class="ui-btn ui-btn-primary">Сохранить</button>
<button class="ui-btn ui-btn-success">Применить</button>
<button class="ui-btn ui-btn-link">Отмена</button>
<button class="ui-btn ui-btn-default ui-btn-disabled">Неактивная</button>

<?php
$form->EndTab();
$form->Buttons();
$form->End();

$tabControl->Begin();
$form->Show();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");