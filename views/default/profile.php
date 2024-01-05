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
<?php if(Yii::$app->settings->get('user', 'bonus_enable')){ ?>
<div class="profile-bonus">
    <div class="profile-bonus-title"><?= Yii::t('user/default', 'BONUS_POINTS'); ?></div>
    <div class="profile-bonus-content">
        <div class="profile-bonus-content-value">
            <span><?= $model->points; ?></span>
        </div>
        <p class="profile-bonus-content-txt"><?= Yii::t('user/default', 'BONUS_INFO'); ?><br/><br/>
            <?php
            if ($model->points_expire) {
                $aggregate = 86400 * (int)Yii::$app->settings->get('user', 'bonus_expire_days');
                ?>
                <?= Yii::t('user/default', 'BONUS_EXPIRE'); ?>:<br/>
                <strong><?= \panix\engine\CMS::date(($model->points_expire + $aggregate), true); ?></strong>
                <br/>
            <?php } ?>
            <?php
            //$pageBonus = \panix\mod\pages\models\Pages::findOne(4);
            ?>
            <?php // Html::a(Yii::t('default', 'MORE_DETAIL'), $pageBonus->getUrl()); ?>
        </p>
    </div>
</div>
<?php } ?>
