<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return array(
    array(
        "parent_menu" => "global_menu_settings", // Родительский раздел: "Настройки" (или укажите другой, например, "global_menu_content" для "Контент")
        "section" => "vendor_projecttemplates",
        "sort" => 100, // Порядок сортировки в меню
        "text" => Loc::getMessage("VENDOR_PROJECTTEMPLATES_MENU_TEXT") ?: "Шаблоны проектов", // Текст пункта (используйте локализацию для многоязычности)
        "title" => Loc::getMessage("VENDOR_PROJECTTEMPLATES_MENU_TITLE") ?: "Настройки шаблонов проектов", // Подсказка
        "url" => "projecttemplates_list.php?lang=" . LANGUAGE_ID, // Ссылка на страницу настроек (создадим её дальше)
        "icon" => "adm_menu_icon_sysupdate", // Иконка (можно взять из стандартных Bitrix или свою)
        "page_icon" => "adm_menu_icon_sysupdate", // Иконка страницы
        "items_id" => "menu_vendor_projecttemplates", // ID для группы
        "items" => array() // Пустой массив для отсутствия подпунктов (пока пустое меню)
    )
);