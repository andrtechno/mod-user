<?php

namespace panix\mod\user\controllers;

use Yii;
use yii\web\Controller;

/**
 * Social auth controller for User module
 */
class AuthController extends Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        Yii::setAlias('@theme', Yii::$app->view->theme->basePath);
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'connect' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'connectCallback'],
                'successUrl' => Yii::$app->homeUrl . 'user/account',
            ],
            'login' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'loginRegisterCallback'],
                'successUrl' => Yii::$app->homeUrl,
            ],
        ];
    }

    /**
     * Connect social auth to the logged-in user
     *
     * @param \yii\authclient\BaseClient $client
     * @return \yii\web\Response
     * @throws \yii\web\ForbiddenHttpException
     */
    public function connectCallback($client)
    {
        // uncomment this to see which attributes you get back

        //print_r($client->getUserAttributes());die;
        // check if user is not logged in. if so, do nothing
        if (Yii::$app->user->isGuest) {
            return;
        }

        // register a new user
        $userAuth = $this->initUserAuth($client);

        $userAuth->setUser(Yii::$app->user->id)->save();
    }

    /**
     * Login/register via social auth
     *
     * @param \yii\authclient\BaseClient $client
     * @return \yii\web\Response
     * @throws \yii\web\ForbiddenHttpException
     */
    public function loginRegisterCallback($client)
    {
        // uncomment this to see which attributes you get back
        //echo "<pre>";print_r($client->getUserAttributes());echo "</pre>";exit;

        // check if user is already logged in. if so, do nothing
        if (!Yii::$app->user->isGuest) {
            return;
        }

        // attempt to log in as an existing user
        if ($this->attemptLogin($client)) {
            return;
        }

        // register a new user
        $userAuth = $this->initUserAuth($client);
        $this->registerAndLoginUser($client, $userAuth);
    }

    /**
     * Initialize a userAuth model based on $client data. Note that we don't set
     * `user_id` yet because that can either be the currently logged in user OR a user
     * matched by email address
     *
     * @param \yii\authclient\BaseClient $client
     * @return \panix\mod\user\models\UserAuth
     */
    protected function initUserAuth($client)
    {
        /** @var \panix\mod\user\models\UserAuth $userAuth */

        // build data. note that we don't set `user_id` yet
        $attributes = $client->getUserAttributes();
        $userAuth = Yii::$app->getModule("user")->model("UserAuth");
        $userAuth->provider = $client->name;
        $userAuth->provider_id = (string)$attributes["id"];

        // parse out google id
        $idCheck = strpos($userAuth->provider_id, "o8/id?id=");
        if ($idCheck !== false) {
            $userAuth->provider_id = substr($userAuth->provider_id, $idCheck + 9);
        }

        $userAuth->setProviderAttributes($attributes);
        return $userAuth;
    }

    /**
     * Attempt to log user in by checking if $userAuth already exists in the db,
     * or if a user already has the email address
     *
     * @param \yii\authclient\BaseClient $client
     * @return bool
     */
    protected function attemptLogin($client)
    {
        $config = Yii::$app->settings->get('user');
        /** @var \panix\mod\user\models\User $user */
        /** @var \panix\mod\user\models\UserAuth $userAuth */
        $user = Yii::$app->getModule("user")->model("User");
        $userAuth = Yii::$app->getModule("user")->model("UserAuth");

        // attempt to find userAuth in database by id and name
        $attributes = $client->getUserAttributes();
        $userAuth = $userAuth::findOne([
            "provider" => $client->name,
            "provider_id" => (string)$attributes["id"],
        ]);
        if ($userAuth) {
            $user = $user::findOne($userAuth->user_id);
            Yii::$app->user->login($user, $config->login_duration);
            return true;
        }

        // call "setInfo{clientName}" function to ensure that we get email consistently
        // this is mainly used for google auth, which returns the email in an array
        // @see setInfoGoogle()
        $function = "setInfo" . ucfirst($client->name);
        $user = $this->$function($attributes);

        // attempt to find user by email
        if (!empty($user["email"])) {

            // check if any user has `new_email` set and clear it
            $email = trim($user["email"]);
            $this->clearNewEmail($email);

            // find user and create user provider for match
            $user = $user::findOne(["email" => $email]);
            if ($user) {
                $userAuth = $this->initUserAuth($client);
                $userAuth->setUser($user->id)->save();
                Yii::$app->user->login($user, $config->login_duration);
                return true;
            }
        }

        return false;
    }

    /**
     * Check to see if any user has changed their email but not yet confirmed it. It will
     * thus be in `user.new_email`, so we need to clear it in case they try to actually
     * confirm the change (which would result in an unique constraint error for email)
     *
     * @param string $email
     */
    protected function clearNewEmail($email)
    {
        /** @var \panix\mod\user\models\User $user */
        /** @var \panix\mod\user\models\UserKey $userKey */
        $user = Yii::$app->getModule("user")->model("User");
        $userKey = Yii::$app->getModule("user")->model("UserKey");

        // attempt to find user with new_email and remove it
        $user = $user::findOne(["new_email" => $email]);
        if ($user) {

            $user->new_email = null;
            $user->save(false);

            // find userKey and consume it
            $userKey = $userKey::findActiveByUser($user->id, $userKey::TYPE_EMAIL_CHANGE);
            if ($userKey) {
                $userKey->consume();
            }
        }
    }

    /**
     * Register a new user using client attributes and then associate userAuth
     *
     * @param \yii\authclient\BaseClient $client
     * @param \panix\mod\user\models\UserAuth $userAuth
     */
    protected function registerAndLoginUser($client, $userAuth)
    {
        /** @var \panix\mod\user\models\User $user */
        $config = Yii::$app->settings->get('user');
        // set user and profile info
        $attributes = $client->getUserAttributes();
        $function = "setInfo" . ucfirst($client->name); // "setInfoFacebook()"
        $user = $this->$function($attributes);

        // calculate and double check username (in case it is already taken)
        $fallbackUsername = "{$client->name}_{$userAuth->provider_id}";
        $user = $this->doubleCheckUsername($user, $fallbackUsername);

        // save new models
        $user->setRegisterAttributes(Yii::$app->request->userIP, $user::STATUS_ACTIVE)->save(false);
        $userAuth->setUser($user->id)->save(false);

        // log user in
        Yii::$app->user->login($user, $config->login_duration);
    }

    /**
     * Double checks username to ensure that it isn't already taken. If so,
     * revert to fallback
     *
     * @param \panix\mod\user\models\User $user
     * @param string $fallbackUsername
     * @return mixed
     */
    protected function doubleCheckUsername($user, $fallbackUsername)
    {
        // replace periods with underscore to match user rules
        //$user->username = str_replace(".", "_", $user->username);

        // check unique username
        $userCheck = $user::findOne(["username" => $user->username]);
        if ($userCheck) {
            $user->username = $fallbackUsername;
        }
        return $user;
    }

    /**
     * Set info for facebook registration
     *
     * @param array $attributes
     * @return \panix\mod\user\models\User
     */
    protected function setInfoFacebook($attributes)
    {
        /** @var \panix\mod\user\models\User $user */
        $user = Yii::$app->getModule("user")->model("User");

        // set email/username if they are set
        // note: email may be missing if user signed up using a phone number
        if (!empty($attributes["email"])) {
            $user->email = $attributes["email"];
            $user->username = $attributes["email"];
        }

        if (!empty($attributes["username"])) {
            //$user->username = $attributes["username"];
            $user->first_name = $attributes["username"];
            $user->last_name = $attributes["username"];
        }

        // use facebook name as username as fallback
        //if (empty($attributes["email"]) && empty($attributes["username"])) {
        //    $user->username = str_replace(" ", "_", $attributes["name"]);
       // }


        return $user;
    }

    /**
     * Set info for twitter registration
     *
     * @param array $attributes
     * @return \panix\mod\user\models\User
     */
    protected function setInfoTwitter($attributes)
    {
        /** @var \panix\mod\user\models\User $user */
        $user = Yii::$app->getModule("user")->model("User");

        $user->username = $attributes["screen_name"];

        return $user;
    }

    /**
     * Set info for google registration
     *
     * @param array $attributes
     * @return \panix\mod\user\models\User
     */
    protected function setInfoGoogle($attributes)
    {
        /** @var \panix\mod\user\models\User $user */
        $user = Yii::$app->getModule("user")->model("User");

        //print_r($attributes);die;
        //$user->email = $attributes["emails"][0]["value"];
        $user->email = $attributes["email"];
        $user->username = $attributes["email"];

        $user->first_name = "{$attributes["given_name"]}";
        $user->last_name = "{$attributes["name"]}";
        //$user->pr = "{$attributes["picture"]}";
        return $user;
    }

    /**
     * Set info for reddit registration
     *
     * @param array $attributes
     * @return \panix\mod\user\models\User
     */
    protected function setInfoReddit($attributes)
    {
        /** @var \panix\mod\user\models\User $user */
        $user = Yii::$app->getModule("user")->model("User");

        $user->username = $attributes["name"];

        return $user;
    }

    /**
     * Set info for LinkedIn registration
     *
     * @param array $attributes
     * @return \panix\mod\user\models\User
     */
    protected function setInfoLinkedIn($attributes)
    {
        /** @var \panix\mod\user\models\User $user */
        $user = Yii::$app->getModule("user")->model("User");

        $user->email = $attributes["email"];
        $user->username = "{$attributes["first_name"]} {$attributes["last_name"]}";

        return $user;
    }

    /**
     * Set info for vkontakte registration
     *
     * @param array $attributes
     * @return \panix\mod\user\models\User
     */
    protected function setInfoVkontakte($attributes)
    {
        /** @var \panix\mod\user\models\User $user */
        $user = Yii::$app->getModule("user")->model("User");

        foreach ($_SESSION as $k => $v) {
            if (is_object($v) && get_class($v) == 'yii\authclient\OAuthToken')
                $user->email = $v->getParam('email');
        }

        // set email/username if they are set
        // note: email may be missing if user signed up using a phone number
        if (!empty($attributes["email"])) {
            $user->email = $attributes["email"];
        }
        if (!empty($attributes["first_name"]) && !empty($attributes["last_name"])) {
            $user->username = $attributes["first_name"] . ' ' . $attributes["last_name"];
        }

        // use vkontakte_id name as username as fallback
        if (empty($attributes["email"]) && empty($attributes["username"])) {
            $user->username = 'vkontakte_' . $attributes["id"];
        }

        // $user->full_name = $attributes["first_name"].' '.$attributes["last_name"];

        return $user;
    }

}
