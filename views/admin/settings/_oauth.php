<?php
/**
 * @var $form panix\engine\bootstrap\ActiveForm
 */
?>

<div class="form-group">
    <div class="text-center">
        <h4><?= (new \panix\engine\authclient\clients\Facebook())->title; ?></h4>
    </div>
</div>
<?= $form->field($model, 'oauth_facebook_id') ?>
<?= $form->field($model, 'oauth_facebook_secret') ?>

<div class="form-group">
    <div class="text-center">
        <h4><?= (new \panix\engine\authclient\clients\TwitterOAuth2())->title; ?></h4>
    </div>
</div>
<?= $form->field($model, 'oauth_twitter_id') ?>
<?= $form->field($model, 'oauth_twitter_secret') ?>

<div class="form-group">
    <div class="text-center">
        <h4><?= (new \panix\engine\authclient\clients\Google())->title; ?></h4>
    </div>
</div>
<?= $form->field($model, 'oauth_google_id') ?>
<?= $form->field($model, 'oauth_google_secret') ?>

<div class="form-group">
    <div class="text-center">
        <h4><?= (new \panix\engine\authclient\clients\VKontakte())->title; ?></h4>
    </div>
</div>
<?= $form->field($model, 'oauth_vkontakte_id') ?>
<?= $form->field($model, 'oauth_vkontakte_secret') ?>

<div class="form-group">
    <div class="text-center">
        <h4><?= (new \panix\engine\authclient\clients\Yandex())->title; ?></h4>
    </div>
</div>
<?= $form->field($model, 'oauth_yandex_id') ?>
<?= $form->field($model, 'oauth_yandex_secret') ?>


<div class="form-group">
    <div class="text-center">
        <h4><?= (new \panix\engine\authclient\clients\Github())->title; ?></h4>
    </div>
</div>
<?= $form->field($model, 'oauth_github_id') ?>
<?= $form->field($model, 'oauth_github_secret') ?>


<div class="form-group">
    <div class="text-center">
        <h4><?= (new \panix\engine\authclient\clients\LinkedIn())->title; ?></h4>
    </div>
</div>

<?= $form->field($model, 'oauth_linkedin_id') ?>
<?= $form->field($model, 'oauth_linkedin_secret') ?>


<div class="form-group">
    <div class="text-center">
        <h4><?= (new \panix\engine\authclient\clients\Live())->title; ?></h4>
    </div>
</div>
<?= $form->field($model, 'oauth_live_id') ?>
<?= $form->field($model, 'oauth_live_secret') ?>

