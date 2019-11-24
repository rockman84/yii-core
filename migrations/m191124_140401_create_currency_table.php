<?php

use yii\db\Migration;
use Yii;
/**
 * Handles the creation of table `{{%currency}}`.
 */
class m191124_140401_create_currency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%currency}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'code' => $this->string(3)->unique()->notNull(),
            'symbol' => $this->string()->defaultValue(null),
            'decimal_point' => $this->string(1)->defaultValue('.'),
            'thousand_separator' => $this->string(1)->defaultValue(','),
            'prefix' => $this->string()->defaultValue(''),
            'suffix' => $this->string()->defaultValue(''),
            'rate' => $this->decimal(20, 10)->defaultValue(1),
            'rate_updated_at' => $this->integer()->unsigned()->defaultValue(null),
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
        $this->dropTable('{{%currency}}');
    }
}
