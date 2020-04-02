<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\User $model
 * @var yii\widgets\ActiveForm $form
 */
$form = ActiveForm::begin(['options' => []]);
?>
<?= $form->field($model, 'current_password')->passwordInput() ?>
<?= $form->field($model, 'new_password')->passwordInput() ?>
<?= $form->field($model, 'new_repeat_password')->passwordInput() ?>
<div class="card-footer text-center">
    <?= Html::submitButton(Yii::t("app/default", "UPDATE"), ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
