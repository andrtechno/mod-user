<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var amnah\yii2\user\models\forms\LoginForm $model
 */

$this->title = Yii::t('user/default', 'Login');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-default-login">

	<h1><?= Html::encode($this->title) ?></h1>

	<p><?= Yii::t("user/default", "Please fill out the following fields to login:") ?></p>

	<?php $form = ActiveForm::begin([
		'id' => 'login-form',
		'options' => ['class' => 'form-horizontal'],
		'fieldConfig' => [
			'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
			'labelOptions' => ['class' => 'col-lg-2 control-label'],
		],

	]); ?>

	<?= $form->field($model, 'username') ?>
	<?= $form->field($model, 'password')->passwordInput() ?>
	<?= $form->field($model, 'rememberMe', [
		'template' => "{label}<div class=\"col-lg-offset-2 col-lg-3\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
	])->checkbox() ?>

	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<?= Html::submitButton(Yii::t('user/default', 'Login'), ['class' => 'btn btn-primary']) ?>

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
		You may login with <strong>neo/neo</strong>.<br>
		To modify the username/password, log in first and then <?= HTML::a("update your account", ["/user/account"]) ?>.
	</div>

</div>
