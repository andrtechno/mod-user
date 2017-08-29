        <?= $form->field($model, 'login_duration') ?>
        <?= $form->field($model, 'enable_register')->checkBox(['label' => null])->label(); ?>
        <?= $form->field($model, 'enable_forgot')->checkBox(['label' => null])->label(); ?>
        <?= $form->field($model, 'enable_social_auth')->checkBox(['label' => null])->label(); ?>