<?php

namespace panix\mod\user\models\forms;

use Yii;
use panix\engine\base\Model;
use panix\mod\user\models\User;
use panix\mod\user\models\UserKey;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User $user
 */
class LoginForm extends Model
{
    protected $module = 'user';
    /**
     * @var string Username and/or email
     */
    public $username;

    /**
     * @var string Password
     */
    public $password;

    /**
     * @var bool If true, users will be logged in for $duration
     */
    public $rememberMe = true;

    /**
     * @var User
     */
    protected $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [["username", "password"], "required"],
            ["username", "validateUser"],
            ["username", "validateUserStatus"],
            ["password", "validatePassword"],
            ["rememberMe", "boolean"],
        ];
    }

    /**
     * Validate user
     */
    public function validateUser()
    {
        // check for valid user
        $user = $this->getUser();
        if (!$user) {
            if (Yii::$app->getModule("user")->loginEmail && Yii::$app->getModule("user")->loginUsername) {
                $attribute = Yii::t('user/default',"Email / Username");
            } else {
                $attribute = Yii::$app->getModule("user")->loginEmail ? "Email" : "Username";
            }
            $this->addError("username", Yii::t("user", "$attribute not found"));
        }
    }

    /**
     * Validate user status
     */
    public function validateUserStatus()
    {
        // check for ban status
        $user = $this->getUser();
        if ($user->ban_time) {
            $this->addError("username", Yii::t("user/default", "USER_BANNED", [
                "reason" => $user->ban_reason,
            ]));
        }

        // check status and resend email if inactive
        if ($user->status == $user::STATUS_INACTIVE) {

            /** @var UserKey $userKey */
          //  $userKey = new UserKey();
            $userKey = UserKey::generate($user->id, UserKey::TYPE_EMAIL_ACTIVATE);
            $user->sendEmailConfirmation($userKey);
            $this->addError("username", Yii::t("user/default", "NO_CONFIRM_EMAIL"));
        }
    }

    /**
     * Validate password
     */
    public function validatePassword()
    {
        // skip if there are already errors
        if ($this->hasErrors()) {
            return;
        }

        // check password
        /** @var \panix\mod\user\models\User $user */
        $user = $this->getUser();
        if (!$user->verifyPassword($this->password)) {
            $this->addError("password", Yii::t("user/default", "Incorrect password"));
        }

    }

    /**
     * Get user based on email and/or username
     *
     * @return User|null
     */
    public function getUser()
    {
        // check if we need to get user
        if ($this->_user === false) {

            // build query based on email and/or username login properties
            $user = User::find();
            if (Yii::$app->getModule("user")->loginEmail) {
                $user->orWhere(["email" => $this->username]);
            }
            if (Yii::$app->getModule("user")->loginUsername) {
                $user->orWhere(["username" => $this->username]);
            }

            // get and store user
            $this->_user = $user->one();
        }

        // return stored user
        return $this->_user;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels2()
    {
        // calculate attribute label for "username"
        if (Yii::$app->getModule("user")->loginEmail && Yii::$app->getModule("user")->loginUsername) {
            $attribute = "Email / Username";
        } else {
            $attribute = Yii::$app->getModule("user")->loginEmail ? "Email" : "Username";
        }

        return [
            "username" => Yii::t("user/default", $attribute),
            "password" => Yii::t("user/default", "Password"),
            "rememberMe" => Yii::t("user/default", "Remember Me"),
        ];
    }

    /**
     * Validate and log user in
     *
     * @param int $duration
     * @return bool
     */
    public function login($duration)
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? $duration : 0);
        }

        return false;
    }
}