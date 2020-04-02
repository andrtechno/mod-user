<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\User $model
 * @var yii\widgets\ActiveForm $form
 */
$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
?>
<?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'subscribe')->checkbox(); ?>
<?= $form->field($model, 'gender')->dropDownList([0 => $model::t('FEMALE'), 1 => $model::t('MALE')], ['prompt' => 'Не указано']); ?>
<?= $form->field($model, 'status')->dropDownList($model::statusDropdown()); ?>
<?= $form->field($model, 'image', [
    'parts' => [
        '{buttons}' => $model->getFileHtmlButton('image')
    ],
    'template' => '{label}{beginWrapper}{input}{buttons}{error}{hint}{endWrapper}'
])->fileInput() ?>
<?= $form->field($model, 'role')->dropDownList($model->getRoles(), ['multiple' => true]); ?>



<?= $form->field($model, 'ban_time')->widget(\panix\engine\jui\DatetimePicker::class, [
    'clientOptions' => [
        'minDate' => new \yii\web\JsExpression('new Date(' . date('Y') . ', ' . (date('n') - 1) . ', ' . date('d') . ')')
    ]
]) ?>
<?= $form->field($model, 'ban_reason')->textarea() ?>

<div class="card-footer text-center">
    <?= $model->submitButton(); ?>
</div>
<?php ActiveForm::end(); ?>
