<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\User $user
 * @var yii\widgets\ActiveForm $form
 */


$tabs[] = [
    'label' => 'Общие',
    'content' => $this->render('_main', ['model' => $user]),
    'active' => true,
    'options' => ['id' => 'main'],
];
if (!$user->isNewRecord) {
    $tabs[] = [
        'label' => Yii::t('user/default', 'CHANGE_PASSWORD'),
        'content' => $this->render('_change-password', ['model' => $changePasswordForm]),
        'headerOptions' => [],
        'options' => ['id' => 'change-password'],
    ];
}
?>

<?php if (!$user->status && !$user->isNewRecord) { ?>
    <div class="alert alert-warning">
        Аккаунет не
        актевирован. <?= Html::a('отправить владельцу письмо с инструкций?', ['send-active', 'id' => $user->id]); ?>
    </div>
<?php } ?>
<div class="card">
    <div class="card-header">
        <h5><?= Html::encode($this->context->pageName) ?></h5>
    </div>
    <div class="card-body">
        <?php
        echo panix\engine\bootstrap\Tabs::widget([
            'items' => $tabs,
        ]);
        ?>
    </div>
</div>
