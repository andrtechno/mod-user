<?php

namespace panix\mod\user\components;

use panix\engine\CMS;
use Yii;
use yii\web\User;
use panix\mod\admin\models\Timeline;

/**
 * User component
 */
class WebUser extends User
{

    /**
     * @inheritdoc
     */
    public $identityClass = 'panix\mod\user\models\User';

    /**
     * @inheritdoc
     */
    public $enableAutoLogin = true;

    /**
     * @inheritdoc
     */
    public $loginUrl = ["/user/default/login"];

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public function getIsLoggedIn()
    {
        return !$this->getIsGuest();
    }

    /**
     * @inheritdoc
     */
    public function afterLogin($identity, $cookieBased, $duration)
    {
        $identity->updateLoginMeta();
        Timeline::add('login');
        parent::afterLogin($identity, $cookieBased, $duration);
    }

    /**
     * @inheritDoc
     */
    public function afterLogout($identity)
    {
        Timeline::add('logout',['user_id'=>$identity->id]);
        parent::afterLogout($identity);
    }

    /**
     * Get user's display name
     *
     * @param string $default
     * @return string
     */
    public function getDisplayName($default = "username")
    {
        $user = $this->getIdentity();
        return $user ? $user->getDisplayName($default) : $this->username;
    }

    public function getLanguage()
    {
        $user = $this->getIdentity();
        return $user ? $user->language : "";
    }

    public function getEmail()
    {
        $user = $this->getIdentity();
        return $user ? $user->email : "";
    }

    public function getTimezone()
    {
        $user = $this->getIdentity();
        return $user ? $user->timezone : NULL;
    }
    public function getLoginTime()
    {
        $user = $this->getIdentity();
        return $user ? $user->login_time : NULL;
    }
    public function getPhone()
    {
        $user = $this->getIdentity();
        return $user ? $user->phone : "";
    }

    public function getLastname()
    {
        $user = $this->getIdentity();
        return $user ? $user->last_name : NULL;
    }

    public function getFirstname()
    {
        $user = $this->getIdentity();
        return $user ? $user->first_name : NULL;
    }


    public function getBanTime()
    {
        $user = $this->getIdentity();
        return $user ? $user->ban_time : false;
    }

    public function getBanReason()
    {
        $user = $this->getIdentity();
        return $user ? $user->ban_reason : false;
    }

    public function getUsername()
    {
        $user = $this->getIdentity();
        return $user ? $user->username : "";
    }

    public function setPoints($value)
    {
        $user = $this->getIdentity();
        if ($user) {
            $user->setPoints($value);
        }
    }

    public function unsetPoints($value)
    {
        $user = $this->getIdentity();
        if ($user) {
            $user->unsetPoints($value);
        }
    }

    /**
     * @param $size
     * @param array $options
     * @return string
     */
    public function getGuestAvatarUrl($size, $options = [])
    {
        return CMS::processImage($size, 'guest.png', '@uploads/users/avatars', $options);
    }

    public function getAvatarUrl($size)
    {
        $user = $this->getIdentity();
        return $user ? $user->getAvatarUrl($size) : "";
    }

    /**
     * Check if user can do $permissionName.
     * If "authManager" component is set, this will simply use the default functionality.
     * Otherwise, it will use our custom permission system
     *
     * @param string $permissionName
     * @param array $params
     * @param bool $allowCaching
     * @return bool
     */
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        // check for auth manager to call parent
        $auth = Yii::$app->getAuthManager();
        if ($auth) {
            if (parent::can('admin', $params, $allowCaching)) {
                return true;
            }
            return parent::can($permissionName, $params, $allowCaching);
        }
        return true;
    }

}
