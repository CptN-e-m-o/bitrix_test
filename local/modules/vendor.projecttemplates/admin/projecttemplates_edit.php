<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Vendor\ProjectTemplates\Table\ProjectTemplateTable;
use Vendor\ProjectTemplates\Table\ProjectTemplateTaskTable;

Loc::loadMessages(__FILE__);

Loader::includeModule('vendor.projecttemplates');

global $APPLICATION;

$APPLICATION->SetTitle(Loc::getMessage('VENDOR_PT_EDIT_TITLE'));

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';

$ID = isset($_GET['ID']) ? (int)$_GET['ID'] : 0;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_bitrix_sessid() && isset($_POST['save'])) {

    $fields = [
        'NAME' => $_POST['NAME'] ?? '',
        'RESPONSIBLE_ID' => (int)($_POST['RESPONSIBLE_ID'] ?? 0),
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

        if (!empty($_POST['TASKS']) && is_array($_POST['TASKS'])) {
            foreach ($_POST['TASKS'] as $taskId => $task) {

                if (!empty($task['DELETE']) && is_numeric($taskId)) {
                    ProjectTemplateTaskTable::delete((int)$taskId);
                    continue;
                }

                $taskFields = [
                    'TEMPLATE_ID' => $ID,
                    'TITLE' => $task['TITLE'] ?? '',
                    'DESCRIPTION' => $task['DESCRIPTION'] ?? '',
                    'RESPONSIBLE_ID' => (int)($task['RESPONSIBLE_ID'] ?? 0),
                    'DEADLINE_OFFSET_DAYS' => (int)($task['DEADLINE_OFFSET_DAYS'] ?? 0),
                ];

                if (is_numeric($taskId)) {
                    ProjectTemplateTaskTable::update((int)$taskId, $taskFields);
                } else {
                    ProjectTemplateTaskTable::add($taskFields);
                }
            }
        }

        LocalRedirect("/bitrix/admin/projecttemplates_list.php");
    }
}

$arData = [];
if ($ID > 0) {
    $arData = ProjectTemplateTable::getById($ID)->fetch();
}
$arTasks = [];
$rsTasks = ProjectTemplateTaskTable::getList([
    'order' => ['ID' => 'ASC'],
    'filter' => ['TEMPLATE_ID' => $ID],
]);

while ($task = $rsTasks->fetch()) {
    $arTasks[] = $task;
}


$aTabs = [
    [
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('VENDOR_PT_TAB_MAIN'),
        'TITLE' => Loc::getMessage('VENDOR_PT_TAB_MAIN_TITLE'),
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
    $tabControl->AddViewField('ID', 'ID', $ID);
    $tabControl->AddViewField('CREATED_AT', Loc::getMessage('VENDOR_PT_CREATED_AT'), $arData['CREATED_AT']);
}

$tabControl->AddEditField(
    'NAME',
    Loc::getMessage('VENDOR_PT_NAME'),
    false,
    ['size' => 50],
    $arData['NAME'] ?? ''
);

$tabControl->AddDropDownField(
    'RESPONSIBLE_ID',
    Loc::getMessage('VENDOR_PT_RESPONSIBLE'),
    false,
    $arUsersOptions,
    $arData['RESPONSIBLE_ID'] ?? ''
);
$tabControl->BeginCustomField('TASKS', Loc::getMessage('VENDOR_PT_TASKS'), true);
?>

    <tr>
        <td colspan="2" style="padding: 0;">

            <div style="margin: 10px 0;">
                <b><?=Loc::getMessage('VENDOR_PT_TASKS')?></b>
            </div>

            <table class="adm-detail-content-table" width="100%" id="tasks-table" style="table-layout: fixed;">
                <colgroup>
                    <col width="25%">
                    <col width="35%">
                    <col width="20%">
                    <col width="10%">
                    <col width="10%">
                </colgroup>

                <tr class="adm-list-table-header">
                    <td><?=Loc::getMessage('VENDOR_PT_TASK_TITLE')?></td>
                    <td><?=Loc::getMessage('VENDOR_PT_TASK_DESCRIPTION')?></td>
                    <td><?=Loc::getMessage('VENDOR_PT_TASK_RESPONSIBLE')?></td>
                    <td><?=Loc::getMessage('VENDOR_PT_TASK_DEADLINE')?></td>
                    <td style="text-align: center;"><?=Loc::getMessage('VENDOR_PT_ACTIONS')?></td>
                </tr>

                <?php foreach ($arTasks as $task):
                    $taskId = (int)$task['ID'];
                    ?>
                    <tr valign="top">

                        <td style="padding: 8px 5px;">
                            <input type="text" name="TASKS[<?= $taskId ?>][TITLE]" value="<?= htmlspecialcharsbx($task['TITLE']) ?>" style="width: 100%; box-sizing: border-box;" />
                        </td>

                        <td style="padding: 8px 5px;">
                            <textarea name="TASKS[<?= $taskId ?>][DESCRIPTION]" rows="6" style="width: 100%; box-sizing: border-box; resize: vertical;"><?= htmlspecialcharsbx($task['DESCRIPTION']) ?></textarea>
                        </td>

                        <td style="padding: 8px 5px;">
                            <?= SelectBoxFromArray(
                                'TASKS['.$taskId.'][RESPONSIBLE_ID]',
                                ['REFERENCE' => array_values($arUsersOptions), 'REFERENCE_ID' => array_keys($arUsersOptions)],
                                $task['RESPONSIBLE_ID'],
                                false,
                                '',
                                'style="width: 100%; box-sizing: border-box;"'
                            ) ?>
                        </td>

                        <td style="padding: 8px 5px;">
                            <input type="number" name="TASKS[<?= $taskId ?>][DEADLINE_OFFSET_DAYS]" value="<?= $task['DEADLINE_OFFSET_DAYS'] ?>" min="0" style="width: 80px;" />
                        </td>

                        <td style="padding: 8px 5px; text-align: center;">
                            <input type="hidden"
                                   name="TASKS[<?= $taskId ?>][DELETE]"
                                   value=""
                                   class="js-task-delete">
                            <input type="button"
                                   class="adm-btn-delete"
                                   value="Удалить"
                                   onclick="deleteTaskRow(this)">
                        </td>


                    </tr>
                <?php endforeach; ?>

            </table>

            <br>
            <input type="button" value="<?=Loc::getMessage('VENDOR_PT_ADD_TASK')?>" onclick="addTaskRow()" class="adm-btn">

        </td>
    </tr>

<?php
$tabControl->EndCustomField("TASKS");

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
?>
    <script>
        let taskIndex = 0;

        function addTaskRow() {
            taskIndex++;

            const table = document.getElementById('tasks-table');
            const row = table.insertRow(-1);

            row.innerHTML = `
        <td style="padding: 8px 5px;">
            <input type="text" placeholder="Введите название задачи" name="TASKS[new${taskIndex}][TITLE]" value="" style="width: 100%; box-sizing: border-box;" />
        </td>
        <td style="padding: 8px 5px;">
            <textarea name="TASKS[new${taskIndex}][DESCRIPTION]" placeholder="Подробное описание задачи..." rows="6" style="width: 100%; box-sizing: border-box; resize: vertical;"></textarea>
        </td>
        <td><?=SelectBoxFromArray(
                'TMP',
                [
                    'REFERENCE' => array_values($arUsersOptions),
                    'REFERENCE_ID' => array_keys($arUsersOptions)
                ],
                ''
            )?></td>
        <td style="padding: 8px 5px;">
            <input type="number" name="TASKS[new${taskIndex}][DEADLINE_OFFSET_DAYS]" value="1" min="0" style="width: 80px;" />
        </td>
        <td>
            <input type="button"
                class="adm-btn-delete"
                value="Удалить"
                onclick="deleteTaskRow(this)">
        </td>
        <td></td>
    `.replace(/TMP/g, `TASKS[new${taskIndex}][RESPONSIBLE_ID]`);
        }

        function deleteTaskRow(button) {
            if (!confirm('Удалить задачу?')) {
                return;
            }

            const row = button.closest('tr');
            const deleteInput = row.querySelector('.js-task-delete');

            if (deleteInput) {
                deleteInput.value = 'Y';
                row.style.display = 'none';
            } else {
                row.remove();
            }
        }
    </script>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';
