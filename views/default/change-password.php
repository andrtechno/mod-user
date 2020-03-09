<?php
use panix\engine\Html;
use yii\bootstrap4\ActiveForm;
//echo Yii::$app->security->generatePasswordHash('admin');
?>


<div class="row">
    <div class="col-lg-5">

        <?php if ($flash = Yii::$app->session->getFlash("change-password-success")) { ?>

            <div class="alert alert-success">
                <?= $flash ?>
            </div>

        <?php } ?>


        <?php $form = ActiveForm::begin(['id' => 'reset-form']); ?>

        <?= $form->field($model, 'current_password')->passwordInput() ?>
        <?= $form->field($model, 'new_password')->passwordInput() ?>
        <?= $form->field($model, 'new_repeat_password')->passwordInput() ?>
        <div class="form-group text-center">
            <?= Html::submitButton(Yii::t("app/default", "UPDATE"), ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

