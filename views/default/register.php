<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\User $user
 */


?>
    <div class="text-center">
        <h1><?= Html::encode($this->context->pageName); ?></h1>
    </div>
<?php if ($flash = Yii::$app->session->getFlash("success")) { ?>
    <div class="alert alert-success">
        <?= $flash ?>
    </div>
<?php } else { ?>
    <div class="col-md-6 offset-md-3 col-lg-4 offset-lg-4">
        <div class="text-muted mb-5"><?= Yii::t("user/default", "REGISTER_HINT") ?></div>
        <?php $form = ActiveForm::begin([
            'id' => 'register-form',
            'fieldConfig' => [
                // 'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
                // 'labelOptions' => ['class' => 'col-lg-2 control-label'],
            ],
            // 'enableAjaxValidation' => true,
        ]); ?>
        <?= $form->field($user, 'email') ?>
        <?= $form->field($user, 'password')->passwordInput() ?>
        <?= $form->field($user, 'password_confirm')->passwordInput() ?>
        <div class="form-group text-center">
            <?= Html::submitButton(Yii::t('user/default', 'REGISTRATION'), ['class' => 'btn btn-success']) ?>
            <?= Html::a(Yii::t('user/default', 'LOGIN'), ["/user/login"], ['class' => 'btn btn-link']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<?php } ?>