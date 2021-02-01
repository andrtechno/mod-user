<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;
use panix\engine\helpers\TimeZoneHelper;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\User $model
 * @var yii\widgets\ActiveForm $form
 */
$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
?>
<?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
<?php // $form->field($model, 'full_name')->textInput(['maxlength' => 255]) ?>
<?php // $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'first_name')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'last_name')->textInput(['maxlength' => 255]) ?>
<?= $form->field($model, 'status')->dropDownList($model::statusDropdown()); ?>
<?= $form->field($model, 'image', [
    'parts' => [
        '{buttons}' => $model->getFileHtmlButton('image')
    ],
    'template' => '<div class="col-sm-4 col-lg-2">{label}</div>{beginWrapper}{input}{buttons}{hint}{error}{endWrapper}'
])->fileInput() ?>
<?= $form->field($model, 'role')->dropDownList($model->getRoles(), ['multiple' => true]); ?>
<?= $form->field($model, 'phone')->widget(\panix\ext\telinput\PhoneInput::class); ?>
<?= $form->field($model, 'subscribe')->checkbox(); ?>
<?= $form->field($model, 'gender')->dropDownList([0 => $model::t('FEMALE'), 1 => $model::t('MALE')], ['prompt' => 'Не указано']); ?>
<?= $form->field($model, 'timezone')->dropDownList(TimeZoneHelper::getTimeZoneData(), ['prompt' => 'Не указано']); ?>
<?= $form->field($model, 'birthday')->widget(\panix\engine\jui\DatePicker::class, [
    'dateFormat' => 'yyyy-MM-dd',
    'clientOptions' => [
        'changeMonth' => true,
        'changeYear' => true,
        'altFormat' => "yy-mm-dd",
        'yearRange' => "1945:" . date('Y')
    ],
    'options' => ['class' => 'form-control']
]) //->textInput(['2data-provide' => 'datepicker']);     ?>
<?= $form->field($model, 'ban_time')->widget(\panix\engine\jui\DatetimePicker::class, [
    'clientOptions' => [
        'minDate' => new \yii\web\JsExpression('new Date(' . date('Y') . ', ' . (date('n') - 1) . ', ' . date('d') . ')')
    ]
]) ?>
<?= $form->field($model, 'ban_reason')->textarea() ?>

<?php if ($model->isNewRecord) { ?>
    <?= $form->field($model, 'new_password')->passwordInput(); ?>
    <?= $form->field($model, 'password_confirm')->passwordInput(); ?>
<?php } ?>
<div class="card-footer text-center">
    <?= $model->submitButton(); ?>
</div>
<?php ActiveForm::end(); ?>
