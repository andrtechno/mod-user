<?php

namespace panix\mod\user\models;

use Yii;

/**
 * This is the model class for table "session_frontend_user".
 *
 * @property string $id
 * @property integer $user_id
 * @property string $ip
 * @property integer $expire
 * @property resource $data
 */
class SessionUser extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%session_user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'ip'], 'required'],
            [['user_id', 'expire'], 'integer'],
            [['data'], 'string'],
            [['id'], 'string', 'max' => 80],
            [['ip'], 'string', 'max' => 15]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'ip' => 'Ip',
            'expire' => 'Expire',
            'data' => 'Data',
        ];
    }

}
