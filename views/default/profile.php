<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\User $model
 */

echo $this->render('_tabs',['']);
?>






<div class="row">
    <div class="col-md-6">
        <div class="user-default-profile">

            <h1><?= Html::encode($this->context->pageName) ?></h1>

            <?php if ($flash = Yii::$app->session->getFlash("profile-success")): ?>

                <div class="alert alert-success">
                    <p><?= $flash ?></p>
                </div>

            <?php endif; ?>

            <?php $form = ActiveForm::begin([
                'id' => 'profile-form',
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    //'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
                    'labelOptions' => ['class' => 'col-lg-22 control-label'],
                ],
                'enableAjaxValidation' => true,
            ]); ?>

            <?= $form->field($model, 'username') ?>

            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10">
                    <?= Html::submitButton(Yii::t('app', 'UPDATE'), ['class' => 'btn btn-primary']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>




        </div>
    </div>
    <div class="col-md-6">
        <h2><?= Yii::t('user/default','Сменить пароль'); ?></h2>
        <?= $this->render('change-password',[
                'model'=>$changePasswordForm
        ]); ?>
    </div>
</div>
