<?php

use yii\helpers\Html;
use panix\engine\bootstrap\ActiveForm;
use panix\mod\user\models\Role;
use panix\mod\admin\models\Languages;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\User $user
 * @var yii\widgets\ActiveForm $form
 */
$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);

?>
    <div class="card">
        <div class="card-header">
            <h5><?= Html::encode($this->context->pageName) ?></h5>
        </div>
        <div class="card-body">
            <?= $form->field($user, 'email')->textInput(['maxlength' => 255]) ?>
            <?= $form->field($user, 'username')->textInput(['maxlength' => 255]) ?>
            <?= $form->field($user, 'subscribe')->checkbox(); ?>
            <?= $form->field($user, 'gender')->dropDownList([0 => $user::t('FEMALE'), 1 => $user::t('MALE')], ['prompt' => 'Не указано']); ?>
            <?= $form->field($user, 'status')->dropDownList($user::statusDropdown()); ?>
            <?= $form->field($user, 'image', [
                'parts' => [
                    '{buttons}' => $user->getFileHtmlButton('image')
                ],
                'template' => '{label}{beginWrapper}{input}{buttons}{error}{hint}{endWrapper}'
            ])->fileInput() ?>
            <?= $form->field($user, 'new_password')->passwordInput() ?>
            <?= $form->field($user, 'role')->dropDownList($user->getRoles(), ['multiple' => true]); ?>
        </div>
        <div class="card-footer text-center">
            <?= $user->submitButton(); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>