<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\forms\LoginForm $model
 */
?>
<div class="row">
<div class="col-md-6 offset-md-3">

	<h1><?= Html::encode($this->context->pageName) ?></h1>

	<p><?= Yii::t("user/default", "Please fill out the following fields to login:") ?></p>

	<?php $form = ActiveForm::begin([
		'id' => 'login-form',
		'options' => ['class' => 'form-horizontal'],
		'fieldConfig' => [
			'template' => "<div class=\"col-lg-5\">{label}</div>\n<div class=\"col-lg-7\">{input}{error}</div>",
			'labelOptions' => ['class' => 'control-label'],
		],

	]); ?>

	<?= $form->field($model, 'username') ?>
	<?= $form->field($model, 'password')->passwordInput() ?>
	<?= $form->field($model, 'rememberMe', [
		'template' => "{label}<div class=\"col-lg-offset-2 col-lg-3\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
	])->checkbox() ?>

	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<?= Html::submitButton(Yii::t('user/default', 'LOGIN'), ['class' => 'btn btn-success']) ?>

            <br/><br/>
            <?= Html::a(Yii::t("user/default", "Register"), ["/user/register"]) ?> /
            <?= Html::a(Yii::t("user/default", "Forgot password") . "?", ["/user/forgot"]) ?> /
            <?= Html::a(Yii::t("user/default", "Resend confirmation email"), ["/user/resend"]) ?>
		</div>
	</div>

	<?php ActiveForm::end(); ?>

    <?php if (Yii::$app->get("authClientCollection", false)): ?>
        <div class="col-lg-offset-2">
            <?= yii\authclient\widgets\AuthChoice::widget([
                'baseAuthUrl' => ['/user/auth/connect']
            ]) ?>
        </div>
    <?php endif; ?>

	<div class="col-lg-offset-2" style="color:#999;">

		To modify the username/password, log in first and then <?= HTML::a("update your account", ["/user/account"]) ?>.
	</div>

</div>
</div>