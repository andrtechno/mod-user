<?php

namespace panix\mod\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tbl_user_auth".
 *
 * @property string $id
 * @property string $user_id
 * @property string $provider
 * @property string $provider_id
 * @property string $provider_attributes
 * @property string $created_at
 * @property string $updatet_at
 *
 * @property User $user
 */
class UserAuth extends ActiveRecord
{
    const MODULE_ID = 'user';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%user_auth}}";
    }

    /**
     * No inputs are used for userAuths
     *
     * @inheritdoc
     */
    /*
      public function rules()
      {
      return [
      [['user_id', 'provider', 'provider_id', 'provider_attributes'], 'required'],
      [['user_id'], 'integer'],
      [['provider_attributes'], 'string'],
      [['create_time', 'update_time'], 'safe'],
      [['provider_id', 'provider'], 'string', 'max' => 255]
      ];
      }
     */

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user/default', 'ID'),
            'user_id' => Yii::t('user/default', 'User ID'),
            'provider' => Yii::t('user/default', 'Provider'),
            'provider_id' => Yii::t('user/default', 'Provider ID'),
            'provider_attributes' => Yii::t('user/default', 'Provider Attributes'),
            'created_at' => Yii::t('user/User', 'CREATED_AT'),
            'updated_at' => Yii::t('user/User', 'UPDATED_AT'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => function () {
                    return date("Y-m-d H:i:s");
                },
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Set user id
     *
     * @param int $userId
     * @return static
     */
    public function setUser($userId)
    {
        $this->user_id = $userId;
        return $this;
    }

    /**
     * Set provider attributes
     *
     * @param array $attributes
     * @return static
     */
    public function setProviderAttributes($attributes)
    {
        $this->provider_attributes = json_encode($attributes);
        return $this;
    }

    /**
     * Get provider attributes
     *
     * @return array
     */
    public function getProviderAttributes()
    {
        return json_decode($this->provider_attributes, true);
    }

}
