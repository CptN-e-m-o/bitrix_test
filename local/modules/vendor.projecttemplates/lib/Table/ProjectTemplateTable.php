<?php

namespace Vendor\ProjectTemplates\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\Type\DateTime;

class ProjectTemplateTable extends DataManager
{
    public static function getTableName()
    {
        return 'vendor_project_templates';
    }

    public static function getMap()
    {
        return [
            new Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new Fields\StringField('NAME', [
                'required' => true,
            ]),
            new Fields\IntegerField('RESPONSIBLE_ID', [
                'required' => true,
            ]),
            new Fields\DatetimeField('CREATED_AT', [
                'default_value' => new DateTime(),
            ]),
        ];
    }
}
