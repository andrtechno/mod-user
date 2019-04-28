<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\forms\LoginForm $model
 */
?>
<div class="row">
    <div class="col-md-6 offset-md-3 col-lg-4 offset-lg-4">
        <div class="text-center">
            <h1><?= Html::encode($this->context->pageName) ?></h1>
        </div>
        <div class="text-muted mb-5"><?= Yii::t("user/default", "LOGIN_HINT") ?></div>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            //'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                //'template' => "<div class=\"col-lg-5\">{label}</div>\n<div class=\"col-lg-7\">{input}{error}</div>",
                'labelOptions' => ['class' => 'col-form-label2'],
            ],

        ]); ?>

        <?= $form->field($model, 'username') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'rememberMe', [
            'template' => "{label}<div class=\"col-lg-offset-2 col-lg-3\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
        ])->checkbox() ?>

        <div class="form-group text-center">

                <?= Html::submitButton(Yii::t('user/default', 'LOGIN'), ['class' => 'btn btn-success']) ?>

                <br/><br/>
                <?= Html::a(Yii::t("user/default", "REGISTER"), ["/user/register"]) ?> /
                <?= Html::a(Yii::t("user/default", "FORGOT") . "?", ["/user/forgot"]) ?>
                <?php //echo Html::a(Yii::t("user/default", "Resend confirmation email"), ["/user/resend"]) ?>

        </div>

        <?php ActiveForm::end(); ?>

        <?php if (Yii::$app->get("authClientCollection", false)) { ?>

                <?= yii\authclient\widgets\AuthChoice::widget([
                    'baseAuthUrl' => ['/user/auth/connect']
                ]) ?>

        <?php } ?>
    </div>
</div>