<?php
\Bitrix\Main\UI\Extension::load(['ui.buttons', 'ui.dialogs.messagebox']);
?>

<?php foreach ($arResult['TEMPLATES'] as $template) { ?>
    <fieldset>
        <legend><?=htmlspecialcharsbx($template['NAME'])?></legend>
        <button class="ui-btn ui-btn-primary"
                onclick="openTemplateConfirm(<?=$template['ID']?>)">
            Создать проект
        </button>
    </fieldset>
<?php } ?>

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
                BX.UI.Dialogs.MessageBox.alert('Ошибка загрузки данных');
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
                        Ответственный: ${BX.util.htmlspecialchars(task.responsible)}<br>
                        Дедлайн: ${task.deadline}
                    </div>
                </li>
                        `).join('') + '</ul>'
                                    : '<div>Задач нет</div>';

                        BX.UI.Dialogs.MessageBox.show({
                            title: 'Создание проекта',
                            message: `
                    <div>
                        <b>Шаблон:</b> ${BX.util.htmlspecialchars(data.name)}<br>
                        <b>Ответственный:</b> ${BX.util.htmlspecialchars(data.responsible)}<br><br>
                        <b>Задачи:</b>
                        ${tasksHtml}
                    </div>
                `,
                buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
                okCaption: 'Создать',
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
                BX.UI.Dialogs.MessageBox.alert('Проект создан, ID: ' + response.data.projectId);
            } else {
                BX.UI.Dialogs.MessageBox.alert('Ошибка при создании проекта');
            }
        });
    }
</script>

