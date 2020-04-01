<?php

namespace panix\mod\user\migrations;

use yii\db\Schema;
use panix\engine\db\Migration;
use panix\mod\user\models\User;
use panix\mod\user\models\UserKey;
use panix\mod\user\models\UserAuth;

class m150214_044831_init_user extends Migration
{
    public $settingsForm = 'panix\mod\user\models\forms\SettingsForm';

    public function up()
    {
        $this->createTable(User::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'image' => $this->string(100)->null(),
            'status' => Schema::TYPE_SMALLINT . ' not null',
            'email' => Schema::TYPE_STRING . ' null default null',
            'full_name' => $this->string(255)->null(),
            'phone' => $this->string(50)->null(),
            'timezone' => $this->string(10)->null(),
            'gender' => $this->tinyInteger(1)->null(),
            'new_email' => Schema::TYPE_STRING . ' null default null',
            'username' => Schema::TYPE_STRING . ' null default null',
            'password' => Schema::TYPE_STRING . ' null default null',
            'auth_key' => Schema::TYPE_STRING . ' null default null',
            'api_key' => Schema::TYPE_STRING . ' null default null',
            'subscribe' => $this->boolean()->defaultValue(1),
            'login_ip' => Schema::TYPE_STRING . ' null default null',
            'login_time' => Schema::TYPE_TIMESTAMP . ' null default null',
            'create_ip' => Schema::TYPE_STRING . ' null default null',
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'ban_time' => Schema::TYPE_TIMESTAMP . ' null default null',
            'ban_reason' => Schema::TYPE_STRING . ' null default null',
        ], $this->tableOptions);

        $this->createTable(UserKey::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'type' => Schema::TYPE_SMALLINT . ' not null',
            'key' => Schema::TYPE_STRING . ' not null',
            'created_at' => Schema::TYPE_TIMESTAMP . ' null default null',
            'consume_time' => Schema::TYPE_TIMESTAMP . ' null default null',
            'expire_time' => Schema::TYPE_TIMESTAMP . ' null default null',
        ], $this->tableOptions);


        $this->createTable(UserAuth::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'provider' => Schema::TYPE_STRING . ' not null',
            'provider_id' => Schema::TYPE_STRING . ' not null',
            'provider_attributes' => Schema::TYPE_TEXT . ' not null',
            'created_at' => Schema::TYPE_TIMESTAMP . ' null default null',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' null default null'
        ], $this->tableOptions);

        // add indexes for performance optimization
        $this->createIndex('email', User::tableName(), 'email', true);
        $this->createIndex('username', User::tableName(), 'username', true);
        $this->createIndex('key', UserKey::tableName(), 'key', true);
        $this->createIndex('provider_id', UserAuth::tableName(), 'provider_id', false);

        // add foreign keys for data integrity
        //$this->addForeignKey('{{%user_key_user_id}}', UserKey::tableName(), 'user_id', User::tableName(), 'id');
        //$this->addForeignKey('{{%user_auth_user_id}}', UserAuth::tableName(), 'user_id', User::tableName(), 'id');

        // insert admin user: admin/admin
        $security = \Yii::$app->security;
        $columns = ['email', 'username', 'password', 'status', 'created_at', 'api_key', 'auth_key'];
        $this->batchInsert(User::tableName(), $columns, [
            [
                'dev@pixelion.com.ua',
                'admin',
                $security->generatePasswordHash('admin'),
                User::STATUS_ACTIVE,
                time(),
                $security->generateRandomString(),
                $security->generateRandomString(),
            ],
        ]);
        $this->loadSettings();

    }

    public function down()
    {
        $this->dropTable(UserAuth::tableName());
        $this->dropTable(UserKey::tableName());
        $this->dropTable(User::tableName());
    }

}
