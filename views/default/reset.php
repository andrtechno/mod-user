<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var panix\mod\user\models\User $user
 * @var bool $success
 * @var bool $invalidKey
 */


?>
<div class="row">
    <div class="col-md-6 offset-md-3 col-lg-4 offset-lg-4">
        <h1><?= Html::encode($this->context->pageName) ?></h1>

        <?php if (!empty($success)) { ?>


            <p><?= Yii::t("user/default", "PASSWORD_RESET_HINT") ?></p>
            <?= Html::a(Yii::t("user/default", "LOGIN"), ["/user/login"], ['class' => 'btn btn-primary']) ?>


        <?php } elseif (!empty($invalidKey)) { ?>

            <div class="alert alert-danger">
                <?= Yii::t("user/default", "INVALID_KEY") ?>
            </div>

        <?php } else { ?>


            <?php $form = ActiveForm::begin(['id' => 'reset-form']); ?>

            <?= $form->field($user, 'new_password')->passwordInput() ?>
            <?= $form->field($user, 'password_confirm')->passwordInput() ?>
            <div class="form-group text-center">
                <?= Html::submitButton(Yii::t("app", "SAVE"), ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>


        <?php } ?>
    </div>
</div>