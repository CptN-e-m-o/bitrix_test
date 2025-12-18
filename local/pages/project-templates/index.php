<?php

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

$APPLICATION->SetTitle('Шаблоны проектов');

$APPLICATION->IncludeComponent(
    'vendor:project.templates',
    '',
    []
);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
