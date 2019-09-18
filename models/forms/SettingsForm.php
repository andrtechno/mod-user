<?php

namespace panix\mod\user\models\forms;

use Yii;
use panix\engine\SettingsModel;

/**
 * Class SettingsForm
 * @package panix\mod\user\models\forms
 */
class SettingsForm extends SettingsModel
{

    public static $category = 'user';
    protected $module = 'user';

    public $login_duration;
    public $enable_register;
    public $enable_forgot;
    public $enable_social_auth;
    public $mail_forgot;

    public $oauth_facebook_id;
    public $oauth_facebook_secret;

    public $oauth_google_id;
    public $oauth_google_secret;

    public $oauth_vkontakte_id;
    public $oauth_vkontakte_secret;

    public $oauth_github_id;
    public $oauth_github_secret;

    public $oauth_yandex_id;
    public $oauth_yandex_secret;

    public $oauth_twitter_id;
    public $oauth_twitter_secret;

    public $oauth_linkedin_id;
    public $oauth_linkedin_secret;

    public $oauth_live_id;
    public $oauth_live_secret;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login_duration'], "required"],
            [['login_duration'], 'integer'],
            [['enable_register', 'enable_forgot', 'enable_social_auth'], 'boolean'],
            [['mail_forgot'], 'string'],
            [['oauth_facebook_id', 'oauth_google_id', 'oauth_vkontakte_id', 'oauth_github_id', 'oauth_yandex_id', 'oauth_twitter_id', 'oauth_linkedin_id', 'oauth_live_id'], 'string'],
            [['oauth_facebook_secret', 'oauth_google_secret', 'oauth_vkontakte_secret', 'oauth_github_secret', 'oauth_yandex_secret', 'oauth_twitter_secret', 'oauth_linkedin_secret', 'oauth_live_secret'], 'string'],
        ];
    }

    /**
     * Настройки по умолчанию
     * @return array
     */
    public static function defaultSettings()
    {
        return [
            'login_duration' => 30,
            'enable_register' => true,
            'enable_forgot' => true,
            'enable_social_auth' => true,
        ];
    }

}