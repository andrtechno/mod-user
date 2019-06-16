<?php

namespace panix\mod\user\migrations;


use yii\db\Schema;
use yii\db\Migration;
use panix\mod\user\models\User;
use panix\mod\user\models\Role;

class m150214_044831_init_user extends Migration {

    public function safeUp() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // create tables. note the specific order
        $this->createTable('{{%user_role}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' not null',
            'created_at' => Schema::TYPE_TIMESTAMP . ' null default null',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' null default null',
            'can_admin' => Schema::TYPE_SMALLINT . ' not null default 0',
                ], $tableOptions);

        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'role_id' => Schema::TYPE_INTEGER . ' not null',
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
            'avatar' => $this->string(50)->null(),
            'login_ip' => Schema::TYPE_STRING . ' null default null',
            'login_time' => Schema::TYPE_TIMESTAMP . ' null default null',
            'create_ip' => Schema::TYPE_STRING . ' null default null',
            'created_at' => Schema::TYPE_TIMESTAMP . ' null default null',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' null default null',
            'ban_time' => Schema::TYPE_TIMESTAMP . ' null default null',
            'ban_reason' => Schema::TYPE_STRING . ' null default null',
                ], $tableOptions);

        $this->createTable('{{%user_key}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' not null',
            'type' => Schema::TYPE_SMALLINT . ' not null',
            'key' => Schema::TYPE_STRING . ' not null',
            'created_at' => Schema::TYPE_TIMESTAMP . ' null default null',
            'consume_time' => Schema::TYPE_TIMESTAMP . ' null default null',
            'expire_time' => Schema::TYPE_TIMESTAMP . ' null default null',
                ], $tableOptions);


        $this->createTable('{{%user_auth}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' not null',
            'provider' => Schema::TYPE_STRING . ' not null',
            'provider_id' => Schema::TYPE_STRING . ' not null',
            'provider_attributes' => Schema::TYPE_TEXT . ' not null',
            'created_at' => Schema::TYPE_TIMESTAMP . ' null default null',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' null default null'
                ], $tableOptions);

        // add indexes for performance optimization
        $this->createIndex('{{%user_email}}', '{{%user}}', 'email', true);
        $this->createIndex('{{%user_username}}', '{{%user}}', 'username', true);
        $this->createIndex('{{%user_key_key}}', '{{%user_key}}', 'key', true);
        $this->createIndex('{{%user_auth_provider_id}}', '{{%user_auth}}', 'provider_id', false);

        // add foreign keys for data integrity
        $this->addForeignKey('{{%user_role_id}}', '{{%user}}', 'role_id', '{{%user_role}}', 'id');
        $this->addForeignKey('{{%user_key_user_id}}', '{{%user_key}}', 'user_id', '{{%user}}', 'id');
        $this->addForeignKey('{{%user_auth_user_id}}', '{{%user_auth}}', 'user_id', '{{%user}}', 'id');

        // insert role data
        $columns = ['name', 'can_admin', 'created_at'];
        $this->batchInsert('{{%user_role}}', $columns, [
            ['Admin', 1, date('Y-m-d H:i:s')],
            ['User', 0, date('Y-m-d H:i:s')],
        ]);

        // insert admin user: neo/neo
        $security = \Yii::$app->security;
        $columns = ['role_id', 'email', 'username', 'password', 'status', 'created_at', 'api_key', 'auth_key'];
        $this->batchInsert('{{%user}}', $columns, [
            [
                Role::ROLE_ADMIN,
                'dev@pixelion.com.ua',
                'admin',
                '$2y$13$dyVw4WkZGkABf2UrGWrhHO4ZmVBv.K4puhOL59Y9jQhIdj63TlV.O',
                User::STATUS_ACTIVE,
                date('Y-m-d H:i:s'),
                $security->generateRandomString(),
                $security->generateRandomString(),
            ],
        ]);
    }

    public function safeDown() {
        // drop tables in reverse order (for foreign key constraints)
        $this->dropTable('{{%user_auth}}');
        $this->dropTable('{{%user_key}}');
        $this->dropTable('{{%user}}');
        $this->dropTable('{{%user_role}}');
    }

}
