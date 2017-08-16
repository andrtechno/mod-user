<?php

namespace panix\mod\user;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\GroupUrlRule;
use panix\engine\WebModule;

/**
 * User module
 *
 * @author amnah <amnah.dev@gmail.com>
 */
class Module extends WebModule implements BootstrapInterface {

    /**
     * @var string Alias for module
     */
    public $alias = "@user";

    /**
     * @var bool If true, users are required to enter an email
     */
    public $requireEmail = true;

    /**
     * @var bool If true, users are required to enter a username
     */
    public $requireUsername = false;

    /**
     * @var bool If true, users can enter an email. This is automatically set to true if $requireEmail = true
     */
    public $useEmail = true;

    /**
     * @var bool If true, users can enter a username. This is automatically set to true if $requireUsername = true
     */
    public $useUsername = true;

    /**
     * @var bool If true, users can log in using their email
     */
    public $loginEmail = true;

    /**
     * @var bool If true, users can log in using their username
     */
    public $loginUsername = true;

    /**
     * @var int Login duration
     */
    public $loginDuration = 2592000; // 1 month

    /**
     * @var array|string|null Url to redirect to after logging in. If null, will redirect to home page. Note that
     *                        AccessControl takes precedence over this (see [[yii\web\User::loginRequired()]])
     */
    public $loginRedirect = null;

    /**
     * @var array|string|null Url to redirect to after logging out. If null, will redirect to home page
     */
    public $logoutRedirect = null;

    /**
     * @var bool If true, users will have to confirm their email address after registering (= email activation)
     */
    public $emailConfirmation = true;

    /**
     * @var bool If true, users will have to confirm their email address after changing it on the account page
     */
    public $emailChangeConfirmation = true;

    /**
     * @var string Time before userKeys expire (currently only used for password resets)
     */
    public $resetKeyExpiration = "48 hours";

    /**
     * @var string Email view path
     */
    public $emailViewPath = "@user/mail";

    /**
     * @var array Model classes, e.g., ["User" => "app\modules\user\models\User"]
     * Usage:
     *   $user = Yii::$app->getModule("user")->model("User", $config);
     *   (equivalent to)
     *   $user = new \aapp\modules\user\models\User($config);
     *
     * The model classes here will be merged with/override the [[getDefaultModelClasses()|default ones]]
     */
    //  public $modelClasses = [];

    /**
     * @var array Storage for models based on $modelClasses
     */
    //  protected $_models;

    public function getNav() {
        return [
            [
                'label' => $this->info['name'],
                "url" => $this->info['url'],
                'icon' => $this->info['icon']
            ],
            [
                'label' => Yii::t('app', 'SETTINGS'),
                "url" => ['/admin/user/settings'],
                'icon' => 'icon-settings'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function init() {


        // check for valid email/username properties
        //  $this->checkModuleProperties();
        // $this->modelClasses = array_merge($this->getDefaultModelClasses(), $this->modelClasses);
        // set alias
        $this->setAliases([
            $this->alias => __DIR__,
        ]);
        /*Yii::$app->i18n->translations['mod/user/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@app/system/modules/user/messages',
            'fileMap' => [
                'mod/user/User' => 'User.php',
                'mod/user/form' => 'form.php',
            ],
        ];*/

        parent::init();
    }

    public static function t($category, $message, $params = [], $language = null) {
        return Yii::t('modules/user/' . $category, $message, $params, $language);
    }

    public function getInfo() {
        return [
            'name' => Yii::t('user/default', 'MODULE_NAME'),
            'author' => 'andrew.panix@gmail.com',
            'version' => '1.0',
            'icon' => 'icon-users',
            'description' => Yii::t('user/default', 'MODULE_DESC'),
            'url' => ['/admin/user'],
        ];
    }

    /**
     * Check for valid email/username properties
     */
    protected function checkModuleProperties() {
        // set use fields based on required fields
        if ($this->requireEmail) {
            $this->useEmail = true;
        }
        if ($this->requireUsername) {
            $this->useUsername = true;
        }

        // get class name for error messages
        $className = get_called_class();

        // check required fields
        if (!$this->requireEmail && !$this->requireUsername) {
            throw new InvalidConfigException("{$className}: \$requireEmail and/or \$requireUsername must be true");
        }
        // check login fields
        if (!$this->loginEmail && !$this->loginUsername) {
            throw new InvalidConfigException("{$className}: \$loginEmail and/or \$loginUsername must be true");
        }
        // check email fields with emailConfirmation/emailChangeConfirmation is true
        if (!$this->useEmail && ($this->emailConfirmation || $this->emailChangeConfirmation)) {
            $msg = "{$className}: \$useEmail must be true if \$email(Change)Confirmation is true";
            throw new InvalidConfigException($msg);
        }

        // ensure that the "user" component is set properly
        // this typically causes problems in the yii2-advanced app
        // when people set it in "common/config" instead of "frontend/config" and/or "backend/config"
        //   -> this results in users failing to login without any feedback/error message
        if (!Yii::$app->request->isConsoleRequest && !Yii::$app->get("user") instanceof \panix\user\components\User) {
            throw new InvalidConfigException('Yii::$app->user is not set properly. It needs to extend \panix\user\components\User');
        }
    }

    /**
     * Get default model classes
     */
    protected function getDefaultModelClasses() {
        return [
            'User' => 'panix\mod\user\models\User',
            'Profile' => 'panix\mod\user\models\Profile',
            'Role' => 'panix\mod\user\models\Role',
            'UserKey' => 'panix\mod\user\models\UserKey',
            'UserAuth' => 'panix\mod\user\models\UserAuth',
            'ForgotForm' => 'panix\mod\user\models\forms\ForgotForm',
            'LoginForm' => 'panix\mod\user\models\forms\LoginForm',
            'ResendForm' => 'panix\mod\user\models\forms\ResendForm',
            'UserSearch' => 'panix\mod\user\models\search\UserSearch',
        ];
    }

    /**
     * Get object instance of model
     *
     * @param string $name
     * @param array  $config
     * @return ActiveRecord
     */
    public function model($name, $config = []) {
        // return object if already created
        if (!empty($this->_models[$name])) {
            return $this->_models[$name];
        }

        // process "Userkey" -> "UserKey" for backwards compatibility
        if ($name === "Userkey") {
            $name = "UserKey";
        }
        // create model and return it
        $className = $this->modelClasses[ucfirst($name)];
        $this->_models[$name] = Yii::createObject(array_merge(["class" => $className], $config));
        return $this->_models[$name];
    }

    /**
     * @inheritdoc
     * NOTE: THIS IS NOT CURRENTLY USED.
     *       This is here for future versions and will need to be bootstrapped via config file
     *
     */
    public function bootstrap($app) {
        // add rules for admin/copy/auth controllers
        $groupUrlRule = new GroupUrlRule([
            'prefix' => $this->id,
            'rules' => [
                '<controller:(admin|copy|auth)>' => '<controller>',
                '<controller:(admin|copy|auth)>/<action:\w+>' => '<controller>/<action>',
                '<action:\w+>' => 'default/<action>',
            ],
        ]);
        $app->getUrlManager()->addRules($groupUrlRule->rules, false);
    }

    /**
     * Modify createController() to handle routes in the default controller
     *
     * This is a temporary hack until they add in url management via modules
     *
     * @link https://github.com/yiisoft/yii2/issues/810
     * @link http://www.yiiframework.com/forum/index.php/topic/21884-module-and-url-management/
     *
     * "user", "user/default", "user/admin", and "user/copy" work like normal
     * any other "user/xxx" gets changed to "user/default/xxx"
     *
     * @inheritdoc
     */
    public function createController($route) {
        // check valid routes
        $validRoutes = [$this->defaultRoute, "admin", "copy", "auth"];
        $isValidRoute = false;
        foreach ($validRoutes as $validRoute) {
            if (strpos($route, $validRoute) === 0) {
                $isValidRoute = true;
                break;
            }
        }

        return (empty($route) or $isValidRoute) ? parent::createController($route) : parent::createController("{$this->defaultRoute}/{$route}");
    }

    /**
     * Get a list of actions for this module. Used for debugging/initial installations
     */
    public function getActions() {
        return [
            "/{$this->id}" => "This 'actions' list. Appears only when <strong>YII_DEBUG</strong>=true, otherwise redirects to /login or /account",
            "/admin/{$this->id}" => "Admin CRUD",
            "/{$this->id}/login" => "Login page",
            "/{$this->id}/logout" => "Logout page",
            "/{$this->id}/register" => "Register page",
            "/{$this->id}/auth/login?authclient=facebook" => "Register/login via social account",
            "/{$this->id}/auth/connect?authclient=facebook" => "Connect social account to currently logged in user",
            "/{$this->id}/account" => "User account page (email, username, password)",
            "/{$this->id}/profile" => "Profile page",
            "/{$this->id}/forgot" => "Forgot password page",
            "/{$this->id}/reset?key=zzzzz" => "Reset password page. Automatically generated from forgot password page",
            "/{$this->id}/resend" => "Resend email confirmation (for both activation and change of email)",
            "/{$this->id}/resend-change" => "Resend email change confirmation (quick link on the 'Account' page)",
            "/{$this->id}/cancel" => "Cancel email change confirmation (quick link on the 'Account' page)",
            "/{$this->id}/confirm?key=zzzzz" => "Confirm email address. Automatically generated upon registration/email change",
        ];
    }

}
