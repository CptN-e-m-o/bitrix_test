<?php
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

\Bitrix\Main\UI\Extension::load(['ui.buttons', 'ui.dialogs.messagebox']);
$this->addExternalCss($this->getFolder() . '/style.css');
?>

<script>
    BX.message(<?=\CUtil::PhpToJSObject([
        'CREATE' => Loc::getMessage('VENDOR_PROJECT_CREATE'),
        'NO_TASKS' => Loc::getMessage('VENDOR_PROJECT_NO_TASKS'),
        'TITLE' => Loc::getMessage('VENDOR_PROJECT_CREATE_TITLE'),
        'TEMPLATE' => Loc::getMessage('VENDOR_PROJECT_TEMPLATE'),
        'RESPONSIBLE' => Loc::getMessage('VENDOR_PROJECT_RESPONSIBLE'),
        'TASKS' => Loc::getMessage('VENDOR_PROJECT_TASKS'),
        'OK' => Loc::getMessage('VENDOR_PROJECT_OK'),
        'LOAD_ERROR' => Loc::getMessage('VENDOR_PROJECT_LOAD_ERROR'),
        'SUCCESS' => Loc::getMessage('VENDOR_PROJECT_CREATE_SUCCESS'),
        'ERROR' => Loc::getMessage('VENDOR_PROJECT_CREATE_ERROR'),
        'TASK_RESPONSIBLE' => Loc::getMessage('VENDOR_PROJECT_TASK_RESPONSIBLE'),
        'TASK_DEADLINE' => Loc::getMessage('VENDOR_PROJECT_TASK_DEADLINE'),
    ])?>);
</script>

<div class="templates-grid">
    <?php foreach ($arResult['TEMPLATES'] as $template) { ?>
        <fieldset class="template-card">
            <legend><?=htmlspecialcharsbx($template['NAME'])?></legend>
            <button
                    class="ui-btn ui-btn-primary"
                    onclick="openTemplateConfirm(<?=$template['ID']?>)"
            >
                <?=Loc::getMessage('VENDOR_PROJECT_CREATE')?>
            </button>
        </fieldset>
    <?php } ?>
</div>

<script>
    function openTemplateConfirm(id) {
        BX.ajax.runComponentAction(
            'vendor:project.templates',
            'getTemplateInfo',
            {
                mode: 'class',
                data: { templateId: id }
            }
        ).then((response) => {
            if (response.status !== 'success') {
                BX.UI.Dialogs.MessageBox.alert(BX.message('LOAD_ERROR'));
                return;
            }

            const data = response.data;

            const tasksHtml = data.tasks.length
                ? '<ul style="margin-top:8px">' + data.tasks.map(task => `
                    <li style="margin-bottom:10px">
                        <div><b>${BX.util.htmlspecialchars(task.title)}</b></div>
                        ${task.description
                ? `<div style="margin:4px 0">${BX.util.htmlspecialchars(task.description)}</div>`
                : ''
            }
                        <div style="font-size:12px;color:#6a737f">
                            ${BX.message('TASK_RESPONSIBLE')}: ${BX.util.htmlspecialchars(task.responsible)}<br>
                            ${BX.message('TASK_DEADLINE')}: ${task.deadline}
                        </div>
                    </li>
                `).join('') + '</ul>'
                : `<div>${BX.message('NO_TASKS')}</div>`;

            BX.UI.Dialogs.MessageBox.show({
                title: BX.message('TITLE'),
                message: `
                    <div>
                        <b>${BX.message('TEMPLATE')}:</b> ${BX.util.htmlspecialchars(data.name)}<br>
                        <b>${BX.message('RESPONSIBLE')}:</b> ${BX.util.htmlspecialchars(data.responsible)}<br><br>
                        <b>${BX.message('TASKS')}:</b>
                        ${tasksHtml}
                    </div>
                `,
                buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
                okCaption: BX.message('OK'),
                onOk: (messageBox) => {
                    messageBox.close();
                    deployTemplate(id);
                }
            });
        });
    }

    function deployTemplate(id) {
        BX.ajax.runComponentAction(
            'vendor:project.templates',
            'deploy',
            {
                mode: 'class',
                data: { templateId: id }
            }
        ).then((response) => {
            if (response.status === 'success' && response.data.success) {
                BX.UI.Dialogs.MessageBox.alert(
                    BX.message('SUCCESS').replace('#ID#', response.data.projectId)
                );
            } else {
                BX.UI.Dialogs.MessageBox.alert(BX.message('ERROR'));
            }
        });
    }
</script>
