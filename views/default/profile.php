<?php

use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \panix\mod\user\models\User $model
 * @var \panix\mod\user\models\forms\ChangePasswordForm $changePasswordForm
 */
?>
<?php if (!$model->status) { ?>
    <div class="alert alert-warning">
        <?= Yii::t('user/default', 'NO_ACTIVE_ACCOUNT', [
            'email' => $model->email,
            'send' => Html::a('Отправить повторно', ['/user/resend', 'email' => $model->email], ['class' => 'btn btn-sm btn-secondary'])
        ]); ?>
    </div>
<?php } ?>
<?php

echo $this->render('_tabs', ['model' => $model, 'changePasswordForm' => $changePasswordForm]);

?>
