<?php

namespace panix\mod\user;

use yii\web\AssetBundle;

class AuthChoiceAsset extends AssetBundle
{
    public $sourcePath = '@user/assets';

    public $depends = [
        'yii\authclient\widgets\AuthChoiceAsset',
        'panix\mod\user\AuthChoiceStyleAsset',
        'yii\web\YiiAsset',
    ];
}
