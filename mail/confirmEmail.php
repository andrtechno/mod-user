<?php

use yii\helpers\Url;

?>

<h3><?= $subject ?></h3>

<p><?= Yii::t("user/default", "Please confirm your email address by clicking the link below:") ?></p>

<p><?= Url::toRoute(["/user/confirm", "key" => $userKey->key], true); ?></p>