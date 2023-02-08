<?php

namespace panix\mod\user\models\forms;

use Yii;
use panix\engine\base\Model;

/**
 * Change password form
 */
class ChangePasswordForm extends Model
{

    protected $module = 'user';
    public $current_password;
    public $new_password;
    public $new_repeat_password;

    /**
     * @var \panix\mod\user\models\User
     */
    protected $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['new_password'], 'string', 'min' => 3, 'on' => ['change', 'setpwd']],
            [['new_password'], 'filter', 'filter' => 'trim', 'on' => ['change', 'setpwd']],
            [['current_password'], 'validateCurrentPassword', 'on' => ['change']],
            [['new_password', 'new_repeat_password'], 'required', 'on' => ['change', 'setpwd']],
            [['current_password'], 'required', 'on' => ['change']],
            [['new_repeat_password'], 'compare', 'compareAttribute' => 'new_password', 'message' => self::t('ERROR_COMPARE_PASSWORDS'), 'on' => ['change', 'setpwd']],
        ];
    }

    /**
     * Get user based on email
     *
     * @return \panix\mod\user\models\User|null
     */
    public function getUser()
    {
        // get and store user
        if ($this->_user === false) {
            $this->_user = Yii::$app->user->identity;
        }
        $this->_user->new_password = $this->new_password;
        return $this->_user;
    }

    public function beforeValidate()
    {
        if ($this->user) {
            if (!$this->user->status) {
                $this->addError('current_password', Yii::t('user/default', 'NOT_ACTIVE_ACCOUNT'));
                $this->addError('new_repeat_password', Yii::t('user/default', 'NOT_ACTIVE_ACCOUNT'));
                $this->addError('new_password', Yii::t('user/default', 'NOT_ACTIVE_ACCOUNT'));
            }
        }
        return parent::beforeValidate();
    }

    public function validateCurrentPassword()
    {

        if (!$this->getUser()->verifyPassword($this->current_password)) {
            $this->addError("current_password", self::t('ERROR_CURRENT_PASSWORD'));
        }
    }

    public function attributeLabels()
    {
        if ($this->scenario == 'setpwd') {
            return [
                'new_password' => Yii::t('user/User', 'PASSWORD'),
                'new_repeat_password' => Yii::t('user/ChangePasswordForm', 'NEW_REPEAT_PASSWORD')
            ];
        }
        return parent::attributeLabels();
    }

}