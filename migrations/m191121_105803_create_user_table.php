<?php

use sky\yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m191121_105803_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique()->defaultValue(null),
            'email_verification_token' => $this->string()->unique()->defaultValue(null),
            'auth_key' => $this->string()->unique()->defaultValue(null),
            'login_attempt' => $this->integer()->defaultValue(0),
            'ip_address' => $this->string()->defaultValue(null),
            'user_agent' => $this->string()->defaultValue(null),
            'role' => $this->integer()->defaultValue(1),
            'status' => $this->smallInteger()->defaultValue(1),
            'created_at' => $this->integer()->unsigned()->defaultValue(null),
            'updated_at' => $this->integer()->unsigned()->defaultValue(null),
        ]);
        
        $this->insert('{{%user}}', [
            'email' => 'root@administrator.com',
            'password_hash' => Yii::$app->security->generatePasswordHash('asdkhu1792yhd19878dashd93'),
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('{{%user}}', [
            'email' => 'admin@admin.com',
            'password_hash' => Yii::$app->security->generatePasswordHash('admin'),
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
