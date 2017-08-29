<?php

namespace panix\mod\user\models\forms;

use panix\engine\SettingsModel;

class SettingsForm extends SettingsModel {

    protected $category = 'user';
    protected $module = 'user';
    public $login_duration;
    public $enable_register;
    public $enable_forgot;
    public $enable_social_auth;
    public $mail_forgot;

    public function rules() {
        return [
            [['login_duration'], "required"],
            [['login_duration'], 'integer'],
            [['enable_register','enable_forgot','enable_social_auth'], 'boolean'],
            [['mail_forgot'],'string']
            
        ];
    }

    /**
     * Настройки по умолчанию
     * @return array
     */
    public function defaultSettings() {
        return [
            'login_duration' => 2592000,
            'enable_register'=>true,
            'enable_forgot'=>true,
            'enable_social_auth'=>true,
        ];
    }

}