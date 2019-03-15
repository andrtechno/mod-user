<?php

namespace panix\mod\user\components;

use Yii;

/**
 * User component
 */
class WebUser extends \yii\web\User {

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
    public $loginUrl = ["/user/login"];

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public function getIsLoggedIn() {
        return !$this->getIsGuest();
    }

    /**
     * @inheritdoc
     */
    public function afterLogin($identity, $cookieBased, $duration) {

        $identity->updateLoginMeta();
        parent::afterLogin($identity, $cookieBased, $duration);
    }

    /**
     * Get user's display name
     *
     * @param string $default
     * @return string
     */
    public function getDisplayName($default = "username") {
        $user = $this->getIdentity();
        return $user ? $user->getDisplayName($default) : $this->username;
    }
    
    public function getLanguage() {
        $user = $this->getIdentity();
        return $user ? $user->language : "";
    }
    
    public function getEmail() {
        $user = $this->getIdentity();
        return $user ? $user->email : "";
    }
    public function getTimezone() {
        $user = $this->getIdentity();
        //return $user ? $user->timezone : NULL;
    }

    public function getPhone() {
        $user = $this->getIdentity();
        return $user ? $user->phone : "";
    }


    public function getUsername() {
        $user = $this->getIdentity();
        return $user ? $user->username : "";
    }

    /**
     * Check if user can do $permissionName.
     * If "authManager" component is set, this will simply use the default functionality.
     * Otherwise, it will use our custom permission system
     *
     * @param string $permissionName
     * @param array  $params
     * @param bool   $allowCaching
     * @return bool
     */
    public function can($permissionName, $params = [], $allowCaching = true) {
        // check for auth manager to call parent
        $auth = Yii::$app->getAuthManager();
        if ($auth) {
            return parent::can($permissionName, $params, $allowCaching);
        }

        // otherwise use our own custom permission (via the role table)

        $user = $this->getIdentity();
        return $user ? $user->can($permissionName) : false;
    }

}
