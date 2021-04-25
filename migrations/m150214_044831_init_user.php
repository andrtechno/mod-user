<?php

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
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'email' => $this->string(255)->null(),
            'full_name' => $this->string(255)->null(),
            'first_name' => $this->string(50)->null(),
            'last_name' => $this->string(50)->null(),
            'middle_name' => $this->string(50)->null(),
            'instagram_url' => $this->string(255)->null(),
            'facebook_url' => $this->string(255)->null(),
            'phone' => $this->phone(),
            'city' => $this->string(255)->null(),
            'timezone' => $this->string(10)->null(),
            'gender' => $this->tinyInteger(1)->null(),
            'new_email' => $this->string(255)->null(),
            'username' => $this->string(255)->null(),
            'password' => $this->string(255)->null(),
            'auth_key' => $this->string(32)->null(),
            'api_key' => $this->string(32)->null(),
            'subscribe' => $this->boolean()->defaultValue(true),
            'login_ip' => $this->string(255)->null(),
            'login_time' => $this->timestamp()->null(),
            'login_user_agent' => $this->text()->null(),
            'ip_create' => $this->string(50)->null(),
            'birthday' => $this->date(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'ban_time' => $this->timestamp()->null(),
            'ban_reason' => $this->string(255)->null(),
            'points' => $this->integer(11)->defaultValue( 0)->comment('Bonus points'),
            'points_expire' => $this->integer()->null()->comment('Bonus points expire'),
        ], $this->tableOptions);

        $this->createTable(UserKey::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'key' => $this->string(255)->notNull(),
            'created_at' => $this->timestamp()->null()->defaultValue(NULL),
            'consume_time' => $this->timestamp()->null()->defaultValue(NULL),
            'expire_time' => $this->timestamp()->null()->defaultValue(NULL),
        ], $this->tableOptions);


        $this->createTable(UserAuth::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'provider' => $this->string(255)->notNull(),
            'provider_id' => $this->string(255)->notNull(),
            'provider_attributes' => $this->text()->notNull(),
            'created_at' => $this->timestamp()->null()->defaultValue(NULL),
            'updated_at' => $this->timestamp()->null()->defaultValue(NULL)
        ], $this->tableOptions);

        // add indexes for performance optimization
        $this->createIndex('email', User::tableName(), 'email', true);
        $this->createIndex('username', User::tableName(), 'username', true);
        $this->createIndex('key', UserKey::tableName(), 'key', true);
        $this->createIndex('provider_id', UserAuth::tableName(), 'provider_id', false);

        $this->createIndex('points_expire', User::tableName(), 'points_expire', false);

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
