<?php

namespace panix\mod\user\migrations;

use panix\engine\components\Settings;
use panix\mod\user\models\forms\SettingsForm;
use yii\db\Schema;
use Yii;
use panix\engine\db\Migration;
use panix\mod\user\models\User;
use panix\mod\user\models\Role;
use panix\mod\user\models\UserKey;
use panix\mod\user\models\UserAuth;

class m150214_044831_init_user extends Migration {

    public function safeUp() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // create tables. note the specific order
        $this->createTable('{{%user_role}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => Schema::TYPE_STRING . ' not null',
            'created_at' => Schema::TYPE_TIMESTAMP . ' null default null',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' null default null',
            'can_admin' => Schema::TYPE_SMALLINT . ' not null default 0',
                ], $tableOptions);

        $this->createTable(User::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'role_id' => $this->integer()->notNull(),
            'image' => $this->string(100)->null(),
            'status' => Schema::TYPE_SMALLINT . ' not null',
            'email' => Schema::TYPE_STRING . ' null default null',
            'phone' => $this->string(50)->null(),
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
                ], $tableOptions);

        $this->createTable(UserKey::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'type' => Schema::TYPE_SMALLINT . ' not null',
            'key' => Schema::TYPE_STRING . ' not null',
            'created_at' => Schema::TYPE_TIMESTAMP . ' null default null',
            'consume_time' => Schema::TYPE_TIMESTAMP . ' null default null',
            'expire_time' => Schema::TYPE_TIMESTAMP . ' null default null',
                ], $tableOptions);


        $this->createTable(UserAuth::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'provider' => Schema::TYPE_STRING . ' not null',
            'provider_id' => Schema::TYPE_STRING . ' not null',
            'provider_attributes' => Schema::TYPE_TEXT . ' not null',
            'created_at' => Schema::TYPE_TIMESTAMP . ' null default null',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' null default null'
                ], $tableOptions);

        // add indexes for performance optimization
        $this->createIndex('{{%user_email}}', User::tableName(), 'email', true);
        $this->createIndex('{{%user_username}}', User::tableName(), 'username', true);
        $this->createIndex('{{%user_key_key}}', UserKey::tableName(), 'key', true);
        $this->createIndex('{{%user_auth_provider_id}}', UserAuth::tableName(), 'provider_id', false);

        // add foreign keys for data integrity
        //$this->addForeignKey('{{%user_role_id}}', User::tableName(), 'role_id', '{{%user_role}}', 'id');
        //$this->addForeignKey('{{%user_key_user_id}}', UserKey::tableName(), 'user_id', User::tableName(), 'id');
        //$this->addForeignKey('{{%user_auth_user_id}}', UserAuth::tableName(), 'user_id', User::tableName(), 'id');

        // insert role data
        $columns = ['name', 'can_admin', 'created_at'];
        $this->batchInsert('{{%user_role}}', $columns, [
            ['Admin', 1, date('Y-m-d H:i:s')],
            ['User', 0, date('Y-m-d H:i:s')],
        ]);

        // insert admin user: neo/neo
        $security = \Yii::$app->security;
        $columns = ['role_id', 'email', 'username', 'password', 'status', 'created_at', 'api_key', 'auth_key'];
        $this->batchInsert(User::tableName(), $columns, [
            [
                Role::ROLE_ADMIN,
                'dev@pixelion.com.ua',
                'admin',
                $security->generatePasswordHash('admin'),
                User::STATUS_ACTIVE,
                time(),
                $security->generateRandomString(),
                $security->generateRandomString(),
            ],
        ]);


        $settings = [];
        foreach (SettingsForm::defaultSettings() as $key => $value) {
            $settings[] = [SettingsForm::$category, $key, $value];
        }

        $this->batchInsert(Settings::tableName(), ['category', 'param', 'value'], $settings);
    }

    public function safeDown() {
        // drop tables in reverse order (for foreign key constraints)
        $this->dropTable(UserAuth::tableName());
        $this->dropTable(UserKey::tableName());
        $this->dropTable(User::tableName());
        $this->dropTable('{{%user_role}}');
    }

}
