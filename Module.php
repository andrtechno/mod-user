<?php

namespace panix\mod\user;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\GroupUrlRule;
use panix\engine\WebModule;
use panix\mod\user\models\forms\SettingsForm;
use panix\mod\admin\widgets\sidebar\BackendNav;

/**
 * Class Module
 * @package panix\mod\user
 *
 * @property array|string|null $loginRedirect
 */
class Module extends WebModule implements BootstrapInterface
{
    public $icon = 'users';
    /**
     * @var string Alias for module
     */

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
     * @var array|string|null Url to redirect to after logging in. If null, will redirect to home page. Note that
     * AccessControl takes precedence over this (see [[yii\web\User::loginRequired()]])
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



    public function getAdminMenu()
    {
        return [
            'user' => [
                'label' => Yii::t('user/default', 'MODULE_NAME'),
                'icon' => $this->icon,
                'items' => [
                    [
                        'label' => Yii::t('user/default', 'MODULE_NAME'),
                        "url" => ['/admin/user'],
                        'icon' => $this->icon
                    ],
                    [
                        'label' => Yii::t('app/default', 'SETTINGS'),
                        "url" => ['/admin/user/settings'],
                        'icon' => 'settings'
                    ]
                ],
            ],
        ];
    }

    public function getAdminSidebar()
    {
        return (new BackendNav())->findMenu($this->id)['items'];
    }

    /**
     * Установка модуля
     * @return boolean
     */
    public function afterInstall()
    {
        Yii::$app->db->import($this->id);

        if (Yii::$app->settings)
            Yii::$app->settings->set($this->id, SettingsForm::defaultSettings());
        return parent::afterInstall();
    }

    /**
     * Удаление модуля
     * @return boolean
     */
    public function afterUninstall()
    {

        Yii::$app->settings->clear($this->id);
        return parent::afterUninstall();
    }

    public function getInfo()
    {
        return [
            'label' => Yii::t('user/default', 'MODULE_NAME'),
            'author' => 'dev@pixelion.com.ua',
            'version' => '1.0',
            'icon' => 'icon-users',
            'description' => Yii::t('user/default', 'MODULE_DESC'),
            'url' => ['/admin/user'],
        ];
    }

    /**
     * Check for valid email/username properties
     */
    protected function checkModuleProperties()
    {
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
        if (!Yii::$app->request->isConsoleRequest && !Yii::$app->get("user") instanceof \panix\mod\user\components\WebUser) {
            throw new InvalidConfigException('Yii::$app->user is not set properly. It needs to extend \panix\user\components\User');
        }
    }
    public function getDefaultModelClasses()
    {
        return [
            'User' => 'panix\mod\user\models\User',
            'ResendForm' => 'panix\mod\user\models\forms\ResendForm',
            'UserKey' => 'panix\mod\user\models\UserKey',
        ];
    }
    /**
     * Get default model classes

    protected function getDefaultModelClasses()
    {
        return [
            'User' => 'panix\mod\user\models\User',
            'Role' => 'panix\mod\user\models\Role',
            'UserKey' => 'panix\mod\user\models\UserKey',
            'UserAuth' => 'panix\mod\user\models\UserAuth',
            'ForgotForm' => 'panix\mod\user\models\forms\ForgotForm',
            'LoginForm' => 'panix\mod\user\models\forms\LoginForm',
            'ResendForm' => 'panix\mod\user\models\forms\ResendForm',
            'UserSearch' => 'panix\mod\user\models\search\UserSearch',
        ];
    }*/

    /**
     * Get object instance of model
     *
     * @param string $name
     * @param array $config
     * @return ActiveRecord

    public function model($name, $config = [])
    {
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
    }*/

    /**
     * @inheritdoc
     * NOTE: THIS IS NOT CURRENTLY USED.
     *       This is here for future versions and will need to be bootstrapped via config file
     *
     */
    public function bootstrap($app)
    {
        $config = $app->settings->get($this->id);
        // add rules for admin/copy/auth controllers
        $groupUrlRule = new GroupUrlRule([
            'prefix' => $this->id,
            'rules' => [
                '<controller:(admin|copy|auth)>' => '<controller>',
                '<controller:(admin|copy|auth)>/<action:\w+>' => '<controller>/<action>',
                '<action:\w+>/authclient/<authclient:[0-9a-zA-Z\-]+>' => 'default/<action>',
                '<action:\w+>' => 'default/<action>',
            ],
        ]);
        $app->getUrlManager()->addRules($groupUrlRule->rules, false);

        $authClientCollection = [];

        if (!empty($config->oauth_google_id) && !empty($config->oauth_google_secret))
            $authClientCollection['clients']['google'] = [
                'class' => 'panix\engine\authclient\clients\Google',
            ];

        if (!empty($config->oauth_facebook_id) && !empty($config->oauth_facebook_secret))
            $authClientCollection['clients']['facebook'] = [
                'class' => 'panix\engine\authclient\clients\Facebook',
            ];

        if (!empty($config->oauth_vkontakte_id) && !empty($config->oauth_vkontakte_secret))
            $authClientCollection['clients']['vkontakte'] = [
                'class' => 'panix\engine\authclient\clients\VKontakte',
            ];

        if (!empty($config->oauth_yandex_id) && !empty($config->oauth_yandex_secret))
            $authClientCollection['clients']['yandex'] = [
                'class' => 'panix\engine\authclient\clients\Yandex',
            ];

        if (!empty($config->oauth_github_id) && !empty($config->oauth_github_secret))
            $authClientCollection['clients']['github'] = [
                'class' => 'panix\engine\authclient\clients\Github',
            ];

        if (!empty($config->oauth_linkedin_id) && !empty($config->oauth_linkedin_secret))
            $authClientCollection['clients']['linkedin'] = [
                'class' => 'panix\engine\authclient\clients\LinkedIn',
            ];

        if (!empty($config->oauth_live_id) && !empty($config->oauth_live_secret))
            $authClientCollection['clients']['live'] = [
                'class' => 'panix\engine\authclient\clients\Live',
            ];


        if (!empty($config->oauth_twitter_id) && !empty($config->oauth_twitter_secret))
            $authClientCollection['clients']['twitter'] = [
                'class' => 'panix\engine\authclient\clients\TwitterOAuth2',
                // for Oauth v1
                /*'attributeParams' => [
                    'include_email' => 'true'
                ]*/
            ];

        if (isset($authClientCollection['clients']) && count($authClientCollection['clients'])) {
            $app->setComponents([
                'authClientCollection' => [
                    'class' => 'yii\authclient\Collection',
                    'clients' => $authClientCollection['clients'],
                ],
            ]);
        }

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

    public function createController2($route)
    {
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
    }*/

    /**
     * Get a list of actions for this module. Used for debugging/initial installations
     */
    public function getActions()
    {
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
