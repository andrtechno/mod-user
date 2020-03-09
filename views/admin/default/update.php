<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\User $user
 * @var yii\widgets\ActiveForm $form
 */
?>


<div class="card">
    <div class="card-header">
        <h5><?= Html::encode($this->context->pageName) ?></h5>
    </div>
    <div class="card-body">
        <?php
        echo panix\engine\bootstrap\Tabs::widget([
            'items' => [
                [
                    'label' => 'Общие',
                    'content' => $this->render('_main', ['model' => $user]),
                    'active' => true,
                    'options' => ['id' => 'main'],
                ],
                [
                    'label' => Yii::t('user/default','CHANGE_PASSWORD'),
                    'content' => $this->render('_change-password', ['model' => $changePasswordForm]),
                    'headerOptions' => [],
                    'options' => ['id' => 'change-password'],
                ]
            ],
        ]);
        ?>
    </div>
</div>
