<?php

namespace panix\mod\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "session_user".
 *
 * @property string $id
 * @property integer $user_id
 * @property string $ip
 * @property integer $expire
 * @property resource $data
 */
class Session extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->session->sessionTable;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'ip' => 'Ip',
            'expire' => 'Expire',
            'data' => 'Data',
        ];
    }

}
