<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tmp_form}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m191126_174659_create_tmp_form_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tmp_form}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string()->unique()->notNull(),
            'user_id' => $this->integer()->defaultValue(null),
            'model_form' => $this->binary()->notNull(),
            'ref_id' => $this->integer()->defaultValue(null),
            'class_name' => $this->string()->defaultValue(null),
            'return_url' => $this->string()->defaultValue(null),
            'session' => $this->string()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->defaultValue(null),
            'updated_at' => $this->integer()->unsigned()->defaultValue(null),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-tmp_form-user_id}}',
            '{{%tmp_form}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-tmp_form-user_id}}',
            '{{%tmp_form}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-tmp_form-user_id}}',
            '{{%tmp_form}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-tmp_form-user_id}}',
            '{{%tmp_form}}'
        );

        $this->dropTable('{{%tmp_form}}');
    }
}
