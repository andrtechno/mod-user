<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var bool $success
 */

$this->title = Yii::t('user/default', $success ? 'Confirmed' : 'Error');
?>
<div class="user-default-confirm">

    <?php if ($success): ?>

        <div class="alert alert-success">

            <?= Yii::t("user/default", "Your email {email} has been confirmed", ["email" => $success]) ?>

            <?php if (Yii::$app->user->isLoggedIn): ?>

                <p><?= Html::a(Yii::t("user/default", "Go to my account"), ["/user/account"]) ?></p>
                <p><?= Html::a(Yii::t("user/default", "Go home"), Yii::$app->getHomeUrl()) ?></p>

            <?php else: ?>

                <p><?= Html::a(Yii::t("user/default", "LOGIN"), ["/user/login"]) ?></p>

            <?php endif; ?>

        </div>


    <?php else: ?>

        <div class="alert alert-danger"><?= Yii::t("user/default", "INVALID_KEY") ?></div>

    <?php endif; ?>

</div>