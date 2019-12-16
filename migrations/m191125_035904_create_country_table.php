<?php
namespace sky\yii\migrations;

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
        
        foreach ($this->data() as $data) {
            $this->insert('{{%country}}', array_merge([
                'created_at' => time(),
                'updated_at' => time(),
            ], $data));
        }
    }
    
    public function data()
    {
        return [
            [
                'name' => 'Indonesia',
                'iso' => 'ID',
                'iso3' => 'IDN',
                'currency_id' => 1,
                'phone_code' => '+62',
            ],
            [
                'name' => 'Singapore',
                'iso' => 'SG',
                'iso3' => 'SGP',
                'currency_id' => 2,
                'phone_code' => '+65',
            ],
            [
                'name' => 'United State',
                'iso' => 'US',
                'iso3' => 'USA',
                'currency_id' => 3,
                'phone_code' => '+1',
            ],
            [
                'name' => 'Malaysia',
                'iso' => 'MY',
                'iso3' => 'MYS',
                'currency_id' => 4,
                'phone_code' => '+60',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%country}}');
    }
}
