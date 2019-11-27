<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%country}}`.
 */
class m191125_035904_create_country_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%country}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'iso' => $this->string(2)->unique()->notNull(),
            'iso3' => $this->string(3)->unique()->notNull(),
            'currency_id' => $this->integer()->defaultValue(null),
            'phone_code' => $this->string()->defaultValue(null),
            'status' => $this->smallInteger()->defaultValue(1),
            'weight' => $this->integer()->defaultValue(null),
            'created_at' => $this->integer()->unsigned()->defaultValue(null),
            'updated_at' => $this->integer()->unsigned()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%country}}');
    }
}
