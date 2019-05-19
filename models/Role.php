<?php

namespace panix\mod\user\models;

use Yii;
use panix\engine\db\ActiveRecord;

/**
 * This is the model class for table "tbl_role".
 *
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property integer $can_admin
 *
 * @property User[] $users
 */
class Role extends ActiveRecord
{
    const MODULE_ID = 'user';
    /**
     * @var int Admin user role
     */
    const ROLE_ADMIN = 1;

    /**
     * @var int Default user role
     */
    const ROLE_USER = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {

        return "{{%user_role}}";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            //            [['create_time', 'update_time'], 'safe'],
            [['can_admin'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'name' => Yii::t('user', 'Name'),
            'create_time' => Yii::t('user', 'Create Time'),
            'update_time' => Yii::t('user', 'Update Time'),
            'can_admin' => Yii::t('user', 'Can Admin'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors2()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => function () {
                    return date("Y-m-d H:i:s");
                },
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create_time',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update_time',
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        $user = Yii::$app->getModule("user")->model("User");
        return $this->hasMany($user::className(), ['role_id' => 'id']);
    }

    /**
     * Check permission
     *
     * @param string $permission
     * @return bool
     */
    public function checkPermission($permission)
    {
        $roleAttribute = "can_{$permission}";
        return $this->$roleAttribute ? true : false;
    }

}
