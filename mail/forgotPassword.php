<?php

use yii\helpers\Url;


?>

<h3><?= $subject ?></h3>

<p><?= Yii::t("user/default", "Please use this link to reset your password:") ?></p>

<p><?= Url::toRoute(["/user/reset", "key" => $userKey->key], true); ?></p>
