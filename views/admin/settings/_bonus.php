<?php
/**
 * @var $this \yii\web\View
 * @var $form panix\engine\bootstrap\ActiveForm
 * @var $model \panix\mod\user\models\forms\SettingsForm
 */

?>
<?= $form->field($model, 'bonus_enable')->checkbox(); ?>
<?= $form->field($model, 'bonus_ratio')->hint('валюта = бал'); ?>
<?= $form->field($model, 'bonus_max_use_order'); ?>
<?= $form->field($model, 'bonus_expire_days'); ?>
<?= $form->field($model, 'bonus_value'); ?>
<?= $form->field($model, 'bonus_register_value'); ?>
<?= $form->field($model, 'bonus_comment_value'); ?>
