<?php

use panix\engine\Html;
use yii\widgets\ActiveForm;
?>
<?php
$form = ActiveForm::begin([
            //  'id' => 'form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-sm-7\">{input}{hint}</div>\n<div class=\"col-sm-10 col-sm-offset-2\">{error}</div>",
                'labelOptions' => ['class' => 'col-sm-5 control-label'],
                ],
        ]);
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?= $this->context->pageName ?></h3>
    </div>
    <div class="panel-body">
        
        <?php
        echo yii\bootstrap\Tabs::widget([
            'items' => [
                [
                    'label' => 'Общие',
                    'content' => $this->render('_main', ['form' => $form, 'model' => $model]),
                    'active' => true,
                    'options' => ['id' => 'main'],
                ],
                [
                    'label' => 'Почта',
                    'content' => $this->render('_mail', ['form' => $form, 'model' => $model]),
                    'headerOptions' => [],
                    'options' => ['id' => 'mail'],
                ],
            ],
        ]);
        ?>

    </div>
    <div class="panel-footer text-center">
        <?= Html::submitButton(Yii::t('app', 'SAVE'), ['class' => 'btn btn-success']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>