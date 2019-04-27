<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\forms\ForgotForm $model
 */

?>

<div class="col-md-6 offset-md-3">
    <div class="text-center">
        <h1><?= Html::encode($this->context->pageName) ?></h1>
    </div>

    <?php if ($flash = Yii::$app->session->getFlash('Forgot-success')) { ?>
        <div class="alert alert-success"><?= $flash ?></div>
    <?php } else { ?>
        <div class="help-block mb-5">
            <?= Yii::t('user/default', 'FORGOT_TEXT'); ?>
        </div>
        <?php $form = ActiveForm::begin(['id' => 'forgot-form']); ?>
        <?= $form->field($model, 'email') ?>
        <div class="form-group text-center">
            <?= Html::submitButton(Yii::t('app', 'SEND'), ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    <?php } ?>
</div>

