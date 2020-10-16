<?php


use panix\mod\pages\models\Pages;
use yii\helpers\ArrayHelper;

/**
 * @var \yii\web\View $this
 * @var \yii\widgets\ActiveForm $form
 * @var \panix\mod\user\models\User $model
 */
?>

<?= $form->field($model, 'login_duration')->hint('Укажите количество суток') ?>

<?= $form->field($model, 'page_agreement')->dropDownList(ArrayHelper::map(Pages::find()->all(), 'id', 'name'), [
    'prompt' => html_entity_decode($model::t('&mdash; не использовать &mdash;'))
]); ?>

<?= $form->field($model, 'enable_register')->checkBox(['label' => null])->label(); ?>
<?= $form->field($model, 'enable_forgot')->checkBox(['label' => null])->label(); ?>
<?= $form->field($model, 'enable_social_auth')->checkBox(['label' => null])->label(); ?>