<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var panix\mod\user\models\User $user
 * @var panix\mod\user\models\Profile $profile
 */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?= Html::encode($this->context->pageName) ?></h3>
    </div>
    <div class="panel-body">
        <?=
        $this->render('_form', [
            'user' => $user,
            'profile' => $profile,
        ])
        ?>
    </div>
</div>
