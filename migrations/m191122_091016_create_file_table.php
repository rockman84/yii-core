<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%file}}`.
 */
class m191122_091016_create_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%file}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'extension' => $this->string(8)->defaultValue(null),
            'content_type' => $this->string()->defaultValue(null),
            'size' => $this->integer()->defaultValue(null),
            'key' => $this->string()->unique()->notNull(),
            'path' => $this->string()->notNull(),
            'object_name' => $this->string()->defaultValue(null),
            'bucket' => $this->string()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->defaultValue(null),
            'updated_at' => $this->integer()->unsigned()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%file}}');
    }
}
