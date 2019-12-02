<?php

use yii\db\Migration;
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
        $time = time();
        foreach ($this->data() as $data) {
            $this->insert('{{%currency}}', array_merge([
                'created_at' => $time,
                'updated_at' => $time,
                'status' => 1
            ], $data));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%currency}}');
    }
    
    public function data()
    {
        return [
            [
                'name' => 'Indonesian Rupiah',
                'code' => 'IDR',
                'symbol' => 'Rp',
                'decimal_point' => ',',
                'thousand_separator' => '.',
                'prefix' => 'Rp',
                'suffix' => '',
            ],
            [
                'name' => 'Singapore Dollar',
                'code' => 'SGD',
                'symbol' => 'SG$',
                'decimal_point' => '.',
                'thousand_separator' => ',',
                'prefix' => 'SG$',
                'suffix' => '',
            ],
            [
                'name' => 'United States Dollar',
                'code' => 'USD',
                'symbol' => 'US$',
                'decimal_point' => '.',
                'thousand_separator' => ',',
                'prefix' => '$',
                'suffix' => '',
            ],
            [
                'name' => 'Malaysia Ringgit',
                'code' => 'MYR',
                'symbol' => 'RM',
                'decimal_point' => '.',
                'thousand_separator' => ',',
                'prefix' => 'RM',
                'suffix' => '',
            ],
            [
                'name' => 'Australian Dollar',
                'code' => 'AUD',
                'symbol' => 'AU$',
                'decimal_point' => '.',
                'thousand_separator' => ',',
                'prefix' => 'AU$',
                'suffix' => '',
            ],
            [
                'name' => 'Japanese Yen',
                'code' => 'JPY',
                'symbol' => '¥',
                'decimal_point' => '.',
                'thousand_separator' => ',',
                'prefix' => '¥',
                'suffix' => '',
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'decimal_point' => '.',
                'thousand_separator' => ',',
                'prefix' => '€',
                'suffix' => '',
            ],
            [
                'name' => 'Pound Sterling',
                'code' => 'GBP',
                'symbol' => '£',
                'decimal_point' => '.',
                'thousand_separator' => ',',
                'prefix' => '£',
                'suffix' => '',
            ],
        ];
    }
}
