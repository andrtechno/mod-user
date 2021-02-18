<?php

use yii\helpers\Html;
use panix\engine\CMS;
use panix\engine\bootstrap\ActiveForm;
use panix\engine\helpers\TimeZoneHelper;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\User $user
 * @var yii\widgets\ActiveForm $form
 */

?>

<?php if (!$user->status && !$user->isNewRecord) { ?>
    <div class="alert alert-warning">
        Аккаунт не активирован. <?= Html::a('отправить владельцу письмо с инструкций?', ['send-active', 'id' => $user->id]); ?>
    </div>
<?php } ?>
<div class="row">
    <div class="col-md-8 col-lg-6 col-xl-8">
        <?php

        $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
        ?>
        <div class="card">
            <div class="card-header">
                <h5><?= Html::encode($this->context->pageName) ?></h5>
            </div>
            <div class="card-body">

                <?= $form->field($user, 'email')->textInput(['maxlength' => 255]) ?>
                <?php // $form->field($model, 'full_name')->textInput(['maxlength' => 255]) ?>
                <?php // $form->field($model, 'username')->textInput(['maxlength' => 255]) ?>
                <?= $form->field($user, 'first_name')->textInput(['maxlength' => 255]) ?>
                <?= $form->field($user, 'last_name')->textInput(['maxlength' => 255]) ?>
                <?= $form->field($user, 'status')->dropDownList($user::statusDropdown()); ?>
                <?= $form->field($user, 'image', [
                    'parts' => [
                        '{buttons}' => $user->getFileHtmlButton('image')
                    ],
                    'template' => '<div class="col-sm-4 col-lg-2">{label}</div>{beginWrapper}{input}{buttons}{hint}{error}{endWrapper}'
                ])->fileInput() ?>
                <?= $form->field($user, 'role')->dropDownList($user->getRoles(), ['multiple' => true]); ?>
                <?= $form->field($user, 'phone')->widget(\panix\ext\telinput\PhoneInput::class); ?>
                <?= $form->field($user, 'points')->textInput(['maxlength' => 255]) ?>
                <?= $form->field($user, 'subscribe')->checkbox(); ?>
                <?= $form->field($user, 'gender')->dropDownList([0 => $user::t('FEMALE'), 1 => $user::t('MALE')], ['prompt' => 'Не указано']); ?>
                <?= $form->field($user, 'timezone')->dropDownList(TimeZoneHelper::getTimeZoneData(), ['prompt' => 'Не указано']); ?>
                <?= $form->field($user, 'birthday')->widget(\panix\engine\jui\DatePicker::class, [
                    'dateFormat' => 'yyyy-MM-dd',
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'altFormat' => "yy-mm-dd",
                        'yearRange' => "1945:" . date('Y')
                    ],
                    'options' => ['class' => 'form-control']
                ]) //->textInput(['2data-provide' => 'datepicker']);     ?>
                <?= $form->field($user, 'ban_time')->widget(\panix\engine\jui\DatetimePicker::class, [
                    'clientOptions' => [
                        'minDate' => new \yii\web\JsExpression('new Date(' . date('Y') . ', ' . (date('n') - 1) . ', ' . date('d') . ')')
                    ]
                ]) ?>
                <?= $form->field($user, 'ban_reason')->textarea() ?>

                <?php if ($user->isNewRecord) { ?>
                    <?= $form->field($user, 'password')->passwordInput(); ?>
                    <?= $form->field($user, 'password_confirm')->passwordInput(); ?>
                <?php } ?>



            </div>
            <div class="card-footer text-center">
                <?= $user->submitButton(); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-4 col-lg-6 col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5>Данные: <?= $user->getDisplayName(); ?></h5>
            </div>
            <div class="card-body">
                <?php
                echo Html::img($user->getAvatarUrl('100x100'), ['class' => 'm-3']);
                ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <?php if ($user->api_key) { ?>
                            <tr>
                                <th style="width: 30%"><?= $user->getAttributeLabel('api_key'); ?></th>
                                <td style="width: 70%"><code><?= $user->api_key; ?></code></td>
                            </tr>
                        <?php } ?>
                        <?php if ($user->ip_create) { ?>
                            <tr>
                                <th style="width: 30%"><?= $user->getAttributeLabel('ip_create'); ?></th>
                                <td style="width: 70%"><?= $user->ip_create; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($user->login_time) { ?>
                            <tr>
                                <th style="width: 30%"><?= $user->getAttributeLabel('login_time'); ?></th>
                                <td style="width: 70%"><?= $user->login_time; ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($user->login_ip) { ?>
                            <tr>
                                <th style="width: 30%"><?= $user->getAttributeLabel('login_ip'); ?></th>
                                <td style="width: 70%"><?= CMS::ip($user->login_ip); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($user->created_at) { ?>
                            <tr>
                                <th style="width: 30%"><?= $user->getAttributeLabel('created_at'); ?></th>
                                <td style="width: 70%"><?= CMS::date($user->created_at); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($user->updated_at) { ?>
                            <tr>
                                <th style="width: 30%"><?= $user->getAttributeLabel('updated_at'); ?></th>
                                <td style="width: 70%"><?= CMS::date($user->updated_at); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($user->login_user_agent) { ?>
                            <tr>
                                <th style="width: 30%"><?= $user->getAttributeLabel('login_user_agent'); ?></th>
                                <td style="width: 70%"><?= new \panix\engine\components\Browser($user->login_user_agent); ?></td>
                            </tr>
                        <?php } ?>
                    </table>

                </div>
                <?php if ($user->session) { ?>
                    <h5>Сессия</h5>
                    <?= $user->session->user_agent; ?>
                <?php } ?>

            </div>
        </div>
    </div>
</div>
