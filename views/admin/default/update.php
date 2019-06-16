<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;
$role = Yii::$app->getModule("user")->model("Role");
/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\User $user
 * @var yii\widgets\ActiveForm $form
 */
?>
<div class="card">
    <div class="card-header">
        <h5><?= Html::encode($this->context->pageName) ?></h5>
    </div>
    <div class="card-body">
        <?php
        $form = ActiveForm::begin();
        ?>

        <?= $form->field($user, 'email')->textInput(['maxlength' => 255]) ?>

        <?= $form->field($user, 'username')->textInput(['maxlength' => 255]) ?>

        <?= $form->field($user, 'new_password')->passwordInput() ?>

        <?= $form->field($user, 'role_id')->dropDownList($role::dropdown()); ?>

        <?= $form->field($user, 'status')->dropDownList($user::statusDropdown()); ?>

        <?php // use checkbox for ban_time ?>
        <?php // convert `ban_time` to int so that the checkbox gets set properly ?>
        <?php $user->ban_time = $user->ban_time ? 1 : 0 ?>
        <?= Html::activeLabel($user, 'ban_time', ['label' => Yii::t('user/default', 'Banned')]); ?>
        <?= Html::activeCheckbox($user, 'ban_time'); ?>
        <?= Html::error($user, 'ban_time'); ?>

        <?= $form->field($user, 'ban_reason'); ?>

        <div class="form-group text-center">
            <?= $user->submitButton(); ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
