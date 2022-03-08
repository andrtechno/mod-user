<?php

namespace panix\mod\user\models;

use panix\engine\CMS;
use panix\engine\Html;
use panix\mod\pages\models\Pages;
use Yii;
use panix\engine\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\helpers\Inflector;
use ReflectionClass;

/**
 * This is the model class for table "tbl_user".
 *
 * @property string $id
 * @property string $role
 * @property integer $status
 * @property string $email
 * @property string $new_email
 * @property string $username
 * @property string $phone
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $auth_key
 * @property string $api_key
 * @property string $login_ip
 * @property string $login_time
 * @property string $login_user_agent
 * @property string $birthday
 * @property string $ip_create
 * @property string $create_time
 * @property string $update_time
 * @property string $ban_time
 * @property string $ban_reason
 * @property int $points
 * @property int $points_expire
 * @property string $language
 * @property string $image
 * @property UserKey[] $userKeys
 * @property UserAuth[] $userAuths
 */
class User extends ActiveRecord implements IdentityInterface
{

    public $disallow_delete = [1];
    const MODULE_ID = 'user';
    const route = '/admin/user/default';
    /**
     * @var int Inactive status
     */
    const STATUS_INACTIVE = 0;

    /**
     * @var int Active status
     */
    const STATUS_ACTIVE = 1;

    /**
     * @var int Unconfirmed email status
     */
    const STATUS_UNCONFIRMED_EMAIL = 2;

    /**
     * @var array Permission cache array
     */
    protected $_access = [];
    public $password_confirm;
    public $new_password;
    public $role;
    //public $new_email;
    public $agreement = false;
    public $currentPassword;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%user}}";
    }

    public function agreement()
    {
        $page = (int)Yii::$app->settings->get('user', 'page_agreement');
        if ($page) {
            $rules = Pages::findOne(['id' => (int)$page]);
            if ($rules) {
                return $rules;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // set initial rules

        $pr = $this->agreement();
        $rules = [];
        if ($pr) {
            $rules[] = ['agreement', 'required', 'requiredValue' => 1, 'message' => self::t('AGREEMENT_MESSAGE'), 'on' => 'register'];
            $rules[] = ['agreement', 'boolean', 'on' => 'register'];
        }
        $rules[] = ['subscribe', 'boolean'];
        // $rules = [
        $rules[] = [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => ['png', 'jpg']];
        // general email and username rules
        $rules[] = [['email', 'username', 'phone', 'first_name', 'last_name', 'middle_name'], 'string', 'max' => 50];
        $rules[] = [['email', 'username'], 'unique'];
        $rules[] = [['email', 'username'], 'filter', 'filter' => 'trim'];
        $rules[] = [['email'], 'email'];
        $rules[] = ['image', 'file'];
        $rules[] = ['birthday', 'date', 'format' => 'php:Y-m-d'];
        $rules[] = ['new_password', 'string', 'min' => 4, 'on' => ['reset', 'change']];
        $rules[] = [['image', 'city', 'instagram_url', 'facebook_url'], 'default'];
        $rules[] = [['instagram_url', 'facebook_url'], 'url'];
        // [['username'], 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u', 'message' => Yii::t('user/default', '{attribute} can contain only letters, numbers, and "_"')],
        // password rules
        //[['newPassword'], 'string', 'min' => 3],
        //[['newPassword'], 'filter', 'filter' => 'trim'],
        $rules[] = [['new_password'], 'required', 'on' => ['reset', 'change']];
        $rules[] = [['password_confirm'], 'required', 'on' => ['register', 'create_user']];
        $rules[] = [['city'], 'string'];
        $rules[] = [['password_confirm', 'password'], 'string', 'min' => 4];
        $rules[] = [['gender', 'points'], 'integer'];
        $rules[] = [['password'], 'required', 'on' => ['register', 'create_user']];
        $rules[] = ['phone', 'panix\ext\telinput\PhoneInputValidator'];
        //[['password_confirm'], 'compare', 'compareAttribute' => 'new_password', 'message' => Yii::t('user/default', 'Passwords do not match')],
        $rules[] = [['password_confirm'], 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('user/default', 'PASSWORD_NOT_MATCH'), 'on' => 'register'];
        // account page
        $rules[] = [['currentPassword'], 'required', 'on' => ['account']];
        $rules[] = [['currentPassword'], 'validateCurrentPassword', 'on' => ['account']];

        // admin rules
        $rules[] = [['ban_time'], 'date', 'format' => 'php:Y-m-d H:i:s', 'on' => ['admin', 'create_user']];
        $rules[] = [['ban_reason'], 'string', 'max' => 255, 'on' => ['admin', 'create_user']];
        $rules[] = [['role', 'username', 'status'], 'required', 'on' => ['admin', 'create_user']];
        //  ];

        // add required rules for email/username depending on module properties
        $requireFields = ["requireEmail", "requireUsername"];
        foreach ($requireFields as $requireField) {
            if (Yii::$app->getModule("user")->$requireField) {
                $attribute = strtolower(substr($requireField, 7)); // "email" or "username"
                $rules[] = [$attribute, "required"];
            }
        }

        return $rules;
    }

    public function setPoints($value, $save = true)
    {
        if ($value) {
            $this->points += floor($value);
            $this->points_expire = time();
            if ($save)
                $this->save(false);
        }
    }

    public function unsetPoints($value, $save = true)
    {
        $this->points -= floor($value);
        if ($this->points <= 0) {
            $this->points_expire = NULL;
        }
        if ($save)
            $this->save(false);


    }

    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            'register_fast' => ['username', 'email', 'phone', 'points', 'points_expire'],
            'register' => ['username', 'email', 'password', 'password_confirm'],
            'reset' => ['new_password', 'password_confirm'],
            'admin' => ['role', 'username', 'points', 'points_expire'],
        ]);
    }

    /**
     * Validate current password (account page)
     */
    public function validateCurrentPassword()
    {
        if (!$this->verifyPassword($this->currentPassword)) {
            $this->addError("currentPassword", "Current password incorrect");
        }
    }


    public function behaviors()
    {
        $a = [];
        $a['uploadFile'] = [
            'class' => '\panix\engine\behaviors\UploadFileBehavior',
            'files' => [
                'image' => '@uploads/user',
            ],
            'options' => [
                'watermark' => false
            ]
        ];
        return ArrayHelper::merge($a, parent::behaviors());
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'new_password' => self::t('NEW_PASSWORD'),
            'password_confirm' => self::t('PASSWORD_CONFIRM'),
            'role' => self::t('ROLE'),
        ]);
    }

    public function getRoles()
    {
        $result = [];
        foreach (Yii::$app->authManager->getRoles() as $role) {
            $result[$role->name] = (!empty($role->description)) ? $role->description : $role->name;
        }
        return $result;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSession()
    {
        return $this->hasOne(Session::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserKeys()
    {
        return $this->hasMany(UserKey::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAuths()
    {
        return $this->hasMany(UserAuth::class, ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(["api_key" => $token]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Verify password
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    public static function find()
    {
        return new UserQuery(get_called_class());
    }
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {

        if ($insert) {
            if (!$this->points) {
                $this->points = 0;
            }
            $this->setPoints(Yii::$app->settings->get('user', 'bonus_register_value'), false);
        }
        // hash new password if set
        if ($this->password && $insert) {
            $this->password = Yii::$app->security->generatePasswordHash($this->password);
        }
        if (in_array($this->scenario, ['reset', 'admin'])) {

            if ($this->new_password)
                $this->password = Yii::$app->security->generatePasswordHash($this->new_password);
        }

        // convert ban_time checkbox to date
        if ($this->ban_time) {
            $this->ban_time = date("Y-m-d H:i:s");
        }

        // ensure fields are null so they won't get set as empty string
        $nullAttributes = ["email", "username", "ban_time", "ban_reason"];
        foreach ($nullAttributes as $nullAttribute) {
            $this->$nullAttribute = $this->$nullAttribute ? $this->$nullAttribute : null;
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->role) {
            Yii::$app->authManager->revokeAll($this->id);
            if (is_array($this->role)) {
                foreach ($this->role as $role) {
                    Yii::$app->authManager->assign(Yii::$app->authManager->getRole($role), $this->id);
                }
            } elseif (is_string($this->role)) {
                Yii::$app->authManager->assign(Yii::$app->authManager->getRole($this->role), $this->id);
            }
        }


        if (Yii::$app->hasModule('mailchimp')) {
            /** @var \DrewM\MailChimp\MailChimp $mailchimp */
            $list = Yii::$app->settings->get('mailchimp', 'list_user');
            if ($list) {
                $mailchimp = Yii::$app->mailchimp->getClient();


                $result = $mailchimp->post('lists/' . $list . '/members', [
                    //'merge_fields' => [
                    //    'FNAME' => $fname,
                    //    'LNAME' => $lname
                    //],
                    'email_address' => $this->email,
                    'status' => 'subscribed',
                ]);

                if ($mailchimp->success()) {
                    // $class   = 'alert-success';
                    // $message = $result['email_address']. ' ' .$result['status'];
                } else {
                    // $class   = 'alert-warning';
                    // $message = $result['title'];
                }
            }


        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function getGenderList()
    {
        return [$this::t('FEMALE'), $this::t('MALE')];
    }

    public function getHiddenFormTokenField()
    {
        $token = Yii::$app->getSecurity()->generateRandomString();
        $token = str_replace('+', '.', base64_encode($token));

        Yii::$app->session->set('_csrf_cms', $token);
        return Html::hiddenInput('_csrf_cms', $token);
    }

    /**
     * Set attributes for registration
     *
     * @param string $userIp
     * @param string $status
     * @return static
     */
    public function setRegisterAttributes($userIp, $status = null)
    {
        // set default attributes
        $attributes = [
            "ip_create" => $userIp,
            "auth_key" => Yii::$app->security->generateRandomString(),
            "api_key" => Yii::$app->security->generateRandomString(),
            "status" => static::STATUS_ACTIVE,
        ];

        // determine if we need to change status based on module properties
        $emailConfirmation = Yii::$app->getModule("user")->emailConfirmation;
        $requireEmail = Yii::$app->getModule("user")->requireEmail;
        $useEmail = Yii::$app->getModule("user")->useEmail;
        if ($status) {
            $attributes["status"] = $status;
        } elseif ($emailConfirmation && $requireEmail) {
            $attributes["status"] = static::STATUS_INACTIVE;
        } elseif ($emailConfirmation && $useEmail && $this->email) {
            $attributes["status"] = static::STATUS_UNCONFIRMED_EMAIL;
        }

        // set attributes and return
        $this->setAttributes($attributes, false);
        return $this;
    }

    /**
     * Check and prepare for email change
     *
     * @return bool True if user set a `new_email`
     */
    public function checkAndPrepEmailChange()
    {
        // check if user is removing email address (only if Module::$requireEmail = false)
        if (trim($this->email) === "") {
            return false;
        }


        // check for change in email
        if ($this->email != $this->getOldAttribute("email")) {

            // change status
            $this->status = static::STATUS_UNCONFIRMED_EMAIL;

            // set `new_email` attribute and restore old one
            $this->new_email = $this->email;
            $this->email = $this->getOldAttribute("email");

            return true;
        }

        return false;
    }

    /**
     * Update login info (ip and time)
     *
     * @return bool
     */
    public function updateLoginMeta()
    {
        // set data
        // $this->login_ip = Yii::$app->getRequest()->getUserIP();
        // $this->login_time = date("Y-m-d H:i:s");
        //$this->login_user_agent = Yii::$app->getRequest()->getUserAgent();
        //$this->setScenario('disallow-timestamp');
        // save and return
        return $this->updateAttributes([
            "login_ip" => Yii::$app->getRequest()->getUserIP(),
            "login_time" => date("Y-m-d H:i:s"),
            "login_user_agent" => Yii::$app->getRequest()->getUserAgent()
        ]);
        //  return $this->save(false, ["login_ip", "login_time", "login_user_agent"]);
    }

    /**
     * Confirm user email
     *
     * @return bool
     */
    public function confirm()
    {
        // update status
        $this->status = static::STATUS_ACTIVE;

        // update new_email if set
        if ($this->new_email) {
            $this->email = $this->new_email;
            $this->new_email = null;
        }
        return $this->updateAttributes([
            "email" => $this->email,
            "new_email" => $this->new_email,
            "status" => static::STATUS_ACTIVE
        ]);
        // save and return
        //return $this->save(false, ["email", "new_email", "status"]);
    }


    /**
     * Get display name for the user
     *
     * @return string|int
     * @var string $default
     */
    public function getDisplayName($default = "")
    {
        // define possible fields
        $possibleNames = [
            ["first_name", 'last_name'],
            "first_name",
            "username",
            "email",
            "id",
        ];

        // go through each and return if valid
        foreach ($possibleNames as $possibleName) {
            if (is_array($possibleName)) {
                $name2 = '';
                foreach ($possibleName as $name) {
                    if (!empty($this->$name)) {
                        $name2 .= $this->$name . ' ';
                    }
                }
                return trim($name2);
            } else {
                if (!empty($this->$possibleName)) {
                    return $this->$possibleName;
                }
            }

        }

        return $default;
    }

    /**
     * Send email confirmation to user
     *
     * @param UserKey $userKey
     * @return int
     */
    public function sendEmailConfirmation($userKey)
    {
        /** @var $mailer \yii\swiftmailer\Mailer */
        /** @var $message \yii\swiftmailer\Message */

        // modify view path to module views
        $mailer = Yii::$app->mailer;
        $oldViewPath = $mailer->viewPath;
        $mailer->viewPath = Yii::$app->getModule("user")->emailViewPath;
        $mailer->htmlLayout = '@app/mail/layouts/empty';
        // send email
        $user = $this;
        $email = $user->new_email !== null ? $user->new_email : $user->email;
        $subject = Yii::t("user/default", "Email Confirmation");
        $message = $mailer->compose('confirmEmail', compact("subject", "user", "userKey"))
            ->setTo($email)
            ->setSubject($subject);

        // check for messageConfig before sending (for backwards-compatible purposes)
        //if (empty($mailer->messageConfig["from"])) {
        //    $message->setFrom(Yii::$app->params["adminEmail"]);
        //}
        $result = $message->send();

        // restore view path and return result
        $mailer->viewPath = $oldViewPath;
        return $result;
    }

    /**
     * Get list of statuses for creating dropdowns
     *
     * @return array
     */
    public static function statusDropdown()
    {
        // get data if needed
        static $dropdown;
        if ($dropdown === null) {

            // create a reflection class to get constants
            $reflClass = new ReflectionClass(get_called_class());
            $constants = $reflClass->getConstants();

            // check for status constants (e.g., STATUS_ACTIVE)
            foreach ($constants as $constantName => $constantValue) {

                // add prettified name to dropdown
                if (strpos($constantName, "STATUS_") === 0) {
                    // $prettyName = str_replace("STATUS_", "", $constantName);
                    // $prettyName = Inflector::humanize(strtolower($prettyName));
                    $dropdown[$constantValue] = self::t($constantName);
                }
            }
        }

        return $dropdown;
    }
    
    
    public function afterDelete()
    {

        UserKey::deleteAll(['user_id' => $this->id]);
        UserAuth::deleteAll(['user_id' => $this->id]);
        $manager = Yii::$app->authManager;
        $roles = $manager->getRolesByUser($this->id);
        foreach ($roles as $role){
            $manager->revoke($role,$this->id);
        }

        parent::afterDelete();
    }

    /**
     * @param bool $size
     * @param array $options
     * @return mixed|string|null
     */
    public function getAvatarUrl($size = false, $options = [])
    {
        if (preg_match('/(http|https):\/\/(.*?)$/i', $this->image)) {
            return $this->image;
        }
        $filesBehavior = $this->getBehavior('uploadFile');
        if ($this->image) {
            return CMS::processImage($size, $this->image, $filesBehavior->files['image'], $options);
        } else {
            return ['/picture', 'text' => $this->getDisplayName()];
            //return CMS::processImage($size, 'user.png', '@uploads/users/avatars', $options);
        }
    }

    public function getProfileUrl()
    {
        return ['/user/profile','id'=>$this->id];
    }
}
