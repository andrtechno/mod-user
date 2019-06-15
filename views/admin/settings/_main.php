<?= $form->field($model, 'login_duration')->hint('В секундах, 2592000 = 30 суток') ?>
<?= $form->field($model, 'enable_register')->checkBox(['label' => null])->label(); ?>
<?= $form->field($model, 'enable_forgot')->checkBox(['label' => null])->label(); ?>
<?= $form->field($model, 'enable_social_auth')->checkBox(['label' => null])->label(); ?>