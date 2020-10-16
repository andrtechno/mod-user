<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\forms\ResendForm $model
 */

?>
<div class="row">
    <div class="col-md-6 offset-md-3">
        <h1><?= Html::encode($this->context->pageName) ?></h1>
        <?php if ($flash = Yii::$app->session->getFlash('success')) { ?>
            <div class="alert alert-success"><?= $flash ?></div>
        <?php } else { ?>
            <?php $form = ActiveForm::begin(['id' => 'resend-form']); ?>
            <?= $form->field($model, 'email') ?>
            <div class="form-group text-center">
                <?= Html::submitButton(Yii::t('app/default', 'SEND'), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        <?php } ?>
    </div>
</div>