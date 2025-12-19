<?php
\Bitrix\Main\UI\Extension::load(['ui.buttons', 'ui.dialogs.messagebox']);
?>

<?php foreach ($arResult['TEMPLATES'] as $template) { ?>
    <div class="ui-card">
        <h3><?=htmlspecialcharsbx($template['NAME'])?></h3>
        <button class="ui-btn ui-btn-primary"
                onclick="deployTemplate(<?=$template['ID']?>)">
            Создать проект
        </button>
    </div>
<?php } ?>

<script>
function deployTemplate(id) {
    BX.ajax.runComponentAction(
        'vendor:project.templates',
        'deploy',
        {
            mode: 'class',
            data: { templateId: id }
        }
    ).then((response) => {
        if(response.status === 'success' && response.data.success) {
            BX.UI.Dialogs.MessageBox.alert('Проект создан, ID: ' + response.data.projectId);
        } else {
            BX.UI.Dialogs.MessageBox.alert('Ошибка при создании проекта');
            console.error(response);
        }
    });
}
</script>
