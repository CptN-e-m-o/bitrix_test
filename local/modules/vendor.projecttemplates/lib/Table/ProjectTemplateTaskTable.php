<?php

namespace Vendor\ProjectTemplates\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;

class ProjectTemplateTaskTable extends DataManager
{
    public static function getTableName()
    {
        return 'vendor_project_template_tasks';
    }

    public static function getMap()
    {
        return [
            new Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new Fields\IntegerField('TEMPLATE_ID', [
                'required' => true,
            ]),
            new Fields\StringField('TITLE', [
                'required' => true,
            ]),
            new Fields\TextField('DESCRIPTION'),
            new Fields\IntegerField('RESPONSIBLE_ID', [
                'required' => true,
            ]),
            new Fields\IntegerField('DEADLINE_OFFSET_DAYS', [
                'required' => true,
            ]),
        ];
    }
}
