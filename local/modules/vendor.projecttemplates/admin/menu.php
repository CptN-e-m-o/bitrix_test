<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return array(
    array(
        "parent_menu" => "global_menu_settings",
        "section" => "vendor_projecttemplates",
        "sort" => 100,
        "text" => Loc::getMessage("VENDOR_PROJECTTEMPLATES_MENU_TEXT") ?: "Шаблоны проектов",
        "title" => Loc::getMessage("VENDOR_PROJECTTEMPLATES_MENU_TITLE") ?: "Настройки шаблонов проектов",
        "url" => "projecttemplates_list.php?lang=" . LANGUAGE_ID,
        "icon" => "adm_menu_icon_sysupdate",
        "page_icon" => "adm_menu_icon_sysupdate",
        "items_id" => "menu_vendor_projecttemplates",
        "items" => array()
    )
);