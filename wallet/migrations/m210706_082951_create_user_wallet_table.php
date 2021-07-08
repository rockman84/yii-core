<?php
namespace sky\yii\wallet\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_wallet}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m210706_082951_create_user_wallet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_wallet}}', [
            'id' => $this->primaryKey(),
            'key_id' => $this->string()->unique()->defaultValue(null),
            'user_id' => $this->integer()->notNull(),
            'currency_id' => $this->integer()->notNull(),
            'value' => $this->decimal(12, 2)->defaultValue(0),
            'expire_at' => $this->integer()->unsigned()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->defaultValue(null),
            'updated_at' => $this->integer()->unsigned()->defaultValue(null),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-user_wallet-user_id}}',
            '{{%user_wallet}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-user_wallet-user_id}}',
            '{{%user_wallet}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `currency_id`
        $this->createIndex(
            '{{%idx-user_wallet-currency_id}}',
            '{{%user_wallet}}',
            'user_id'
        );

        // add foreign key for table `{{%currency}}`
        $this->addForeignKey(
            '{{%fk-user_wallet-currency_id}}',
            '{{%user_wallet}}',
            'currency_id',
            '{{%currency}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%currency}}`
        $this->dropForeignKey(
            '{{%fk-user_wallet-currency_id}}',
            '{{%user_wallet}}'
        );

        // drops index for column `currency_id`
        $this->dropIndex(
            '{{%idx-user_wallet-currency_id}}',
            '{{%user_wallet}}'
        );

        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-user_wallet-user_id}}',
            '{{%user_wallet}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-user_wallet-user_id}}',
            '{{%user_wallet}}'
        );

        $this->dropTable('{{%user_wallet}}');
    }
}
