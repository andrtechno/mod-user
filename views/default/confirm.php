<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var bool $success
 */

$this->title = Yii::t('user/default', $success ? 'CONFIRMED' : 'ERROR');
?>
<div class="user-default-confirm">

    <?php if ($success){ ?>

        <div class="alert alert-success">
            <?= Yii::t("user/default", "EMAIL_CONFIRMED", ["email" => $success]) ?>
            <?php if (Yii::$app->user->isLoggedIn) { ?>
                <p><?= Html::a(Yii::t("user/default", "GO_TO_PROFILE"), ["/user/account"]) ?></p>
                <p><?= Html::a(Yii::t("user/default", "GO_HOME"), Yii::$app->getHomeUrl(), ['class' => 'btn btn-outline-secondary']) ?></p>
            <?php } else { ?>
                <p><?= Html::a(Yii::t("user/default", "LOGIN"), ["/user/login"], ['class' => 'btn btn-outline-secondary']) ?></p>
            <?php } ?>
        </div>


    <?php }else{ ?>

        <div class="alert alert-danger"><?= Yii::t("user/default", "INVALID_KEY") ?></div>

    <?php } ?>

</div>