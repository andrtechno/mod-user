<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var panix\mod\user\models\forms\ResendForm $model
 */

$this->view->title = Yii::t('user/default', 'Resend');
$this->params['breadcrumbs'][] = $this->view->title;
?>
<div class="user-default-resend">

	<h1><?= Html::encode($this->view->title) ?></h1>

	<?php if ($flash = Yii::$app->session->getFlash('Resend-success')): ?>

        <div class="alert alert-success">
            <p><?= $flash ?></p>
        </div>

	<?php else: ?>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'resend-form']); ?>
                    <?= $form->field($model, 'email') ?>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('user/default', 'Submit'), ['class' => 'btn btn-primary']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

	<?php endif; ?>

</div>