<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\forms\LoginForm $model
 */
?>

    <div class="main-content login-page">
        <div class="container">
            <?php foreach (Yii::$app->session->getAllFlashes() as $key => $flesh) { ?>
                <?php if (is_array($flesh)) {

                    ?>
                    <?php foreach ($flesh as $message) { ?>
                        <div class="alert alert-<?= ($key == 'error') ? 'danger' : $key; ?>"><?= $message; ?></div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="alert alert-<?= ($key == 'error') ? 'danger' : $key; ?>"><?= $flesh; ?></div>
                <?php } ?>

            <?php } ?>
            <div class="login-register-form row">

                <div class="col-sm-6 col-xs-12">
                    <div class="wrap_inner">
                        <div class="content-login">


                            <?php $formLogin = ActiveForm::begin([
                                'id' => 'login-form',
                                'action' => ['sign'],
                                'options' => ['class' => 'login'],
                                'fieldConfig' => [
                                    //    'template' => "<div class=\"col-sm-9\">{input}{error}</div>",
                                    //     'labelOptions' => ['class' => 'col-sm-3 col-form-label'],
                                    'horizontalCssClasses' => [
                                        'label' => 'col-sm-3',
                                        'offset' => 'offset-sm-3',
                                        'wrapper' => 'col-sm-9',
                                        'error' => '',
                                        'hint' => '',
                                    ],
                                ],
                                'layout' => ActiveForm::LAYOUT_DEFAULT,


                            ]); ?>
                            <div class="login-form">
                                <h4 class="title"><?= Yii::t('user/default', 'LOGIN'); ?></h4>
                                <p class="des"><?= Yii::t("user/default", "LOGIN_HINT") ?></p>
                                <?= $formLogin->field($loginModel, 'username')->textInput(['placeholder' => $loginModel->getAttributeLabel('username')]) ?>
                                <?= $formLogin->field($loginModel, 'password')->passwordInput(['placeholder' => $loginModel->getAttributeLabel('password')]) ?>


                                <div class="form-group clearfix">
                                    <div class="inline pull-left">
                                        <?= $formLogin->field($loginModel, 'rememberMe', [
                                            'template' => "{label}<div class=\"col-lg-offset-2 col-lg-3\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
                                        ])->checkbox() ?>
                                    </div>

                                    <div class="lost_password pull-right">
                                        <?= Html::a(Yii::t("user/default", "FORGOT") . "?", ["/user/forgot"], ['class' => 'forgot']) ?>
                                    </div>

                                </div>

                                <div class="form-group wrapper-submit">

                                    <?= Html::submitButton(Yii::t('user/default', 'LOGIN'), ['class' => 'btn btn-theme btn-block']) ?>
                                </div>

                            </div>
                            <?php ActiveForm::end(); ?>

                            <?php if (Yii::$app->get("authClientCollection", false)) { ?>

                                <?= \panix\mod\user\AuthChoice::widget([
                                    'baseAuthUrl' => ['/user/auth/login']
                                ]) ?>

                            <?php } ?>


                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="wrap_inner">
                        <div class="content-login">
                            <h4 class="title"><?= Yii::t('user/default', 'REGISTER'); ?></h4>

                            <?php $formRegister = ActiveForm::begin([
                                'id' => 'register-form',
                                'action' => ['register'],
                                'options' => ['class' => 'register'],
                                'fieldConfig' => [
                                    //    'template' => "<div class=\"col-sm-9\">{input}{error}</div>",
                                    //     'labelOptions' => ['class' => 'col-sm-3 col-form-label'],
                                    'horizontalCssClasses' => [
                                        'label' => 'col-sm-4',
                                        'offset' => 'offset-sm-4',
                                        'wrapper' => 'col-sm-8',
                                        'error' => '',
                                        'hint' => '',
                                    ],
                                ],
                                'layout' => ActiveForm::LAYOUT_DEFAULT,


                            ]); ?>

                            <?= $formRegister->field($registerModel, 'email')->textInput(['placeholder' => $loginModel->getAttributeLabel('email')]) ?>
                            <?= $formRegister->field($registerModel, 'password')->passwordInput(['placeholder' => $loginModel->getAttributeLabel('password')]) ?>
                            <?= $formRegister->field($registerModel, 'password_confirm')->passwordInput(['placeholder' => $loginModel->getAttributeLabel('password_confirm')]) ?>

                            <div class="form-group wrapper-submit">

                                <?= Html::submitButton(Yii::t('user/default', 'REGISTRATION'), ['class' => 'btn btn-theme btn-block']) ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php


$this->registerJs("



$(document).on(\"beforeValidate\", \"form\", function(event, messages, deferreds) {
    $(this).find(':submit').attr('disabled', true);
    //console.log('BEFORE VALIDATE TEST');
}).on(\"afterValidate\", \"form\", function(event, messages, errorAttributes) {
    //console.log('AFTER VALIDATE TEST');
    if (errorAttributes.length > 0) {
        $(this).find(':submit').attr('disabled', false);
    }
});
$(document).on(\"beforeSubmit\", \"form\", function (event, messages) {
    //console.log('Test new form');
        var that = $(this);
        var data = that.serialize();
        var url = that.attr('action');
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: data
        })
        .done(function(response) {
            if (response.success == true) {

                if(response.redirect){
                    window.location.href = response.redirect;
                }else{
                    common.notify(response.message,'success');
                }
            }else{
                $.each(response, function(index, error){
                    common.notify(error[0],'error');
                });
            }
            that.find(':submit').attr('disabled', false);
        })
        .fail(function(err) {
            that.find(':submit').attr('disabled', false);
        });

    return false;
});


    /*$(\"#register-form\").submit(function(event) {
        event.preventDefault(); // stopping submitting
        var data = $(this).serialize();
        var url = $(this).attr('action');
        $.ajax({
            url: url,
            type: 'post',
            dataType: 'json',
            data: data
        })
        .done(function(response) {
            if (response.success == true) {
                common.notify(response.message,'success');
            }else{
                $.each(response, function(index, error){
                    console.log(error)
                    common.notify(error[0],'error');
                });
            }
            $('#register-form').find(':submit').attr('disabled', false);
        })
        .fail(function(err) {
            console.log(\"error\", err);
            $('#register-form').find(':submit').attr('disabled', false);
        });
    });*/
", \yii\web\View::POS_END);