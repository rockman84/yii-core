<?php
namespace sky\yii\wallet\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_wallet_history}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user_wallet}}`
 */
class m210706_083756_create_user_wallet_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_wallet_history}}', [
            'id' => $this->primaryKey(),
            'user_wallet_id' => $this->integer()->notNull(),
            'ref_id' => $this->integer()->defaultValue(null),
            'model_class' => $this->string()->defaultValue(null),
            'note' => $this->string()->defaultValue(null),
            'operators' => $this->smallInteger()->notNull(),
            'value' => $this->decimal(12, 2)->defaultValue(0),
            'old_wallet' => $this->decimal(12, 2)->defaultValue(0),
            'new_wallet' => $this->decimal(12, 2)->defaultValue(0),
            'created_at' => $this->integer()->unsigned()->defaultValue(null),
        ]);

        // creates index for column `user_wallet_id`
        $this->createIndex(
            '{{%idx-user_wallet_history-user_wallet_id}}',
            '{{%user_wallet_history}}',
            'user_wallet_id'
        );

        // add foreign key for table `{{%user_wallet}}`
        $this->addForeignKey(
            '{{%fk-user_wallet_history-user_wallet_id}}',
            '{{%user_wallet_history}}',
            'user_wallet_id',
            '{{%user_wallet}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user_wallet}}`
        $this->dropForeignKey(
            '{{%fk-user_wallet_history-user_wallet_id}}',
            '{{%user_wallet_history}}'
        );

        // drops index for column `user_wallet_id`
        $this->dropIndex(
            '{{%idx-user_wallet_history-user_wallet_id}}',
            '{{%user_wallet_history}}'
        );

        $this->dropTable('{{%user_wallet_history}}');
    }
}
