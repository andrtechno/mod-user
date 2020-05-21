<?php


namespace panix\mod\user;

use panix\engine\web\AssetBundle;

class AuthChoiceStyleAsset extends AssetBundle
{
    public $sourcePath = '@user/assets';
    public $css = [
        'css/authchoice.css',
    ];
}