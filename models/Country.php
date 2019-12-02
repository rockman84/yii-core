<?php

namespace sky\yii\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "country".
 *
 * @property int $id
 * @property string $name
 * @property string $iso
 * @property string $iso3
 * @property int|null $currency_id
 * @property string|null $phone_code
 * @property int|null $status
 * @property int|null $weight
 * @property int|null $created_at
 * @property int|null $updated_at
 * 
 * @property Currency $currency
 */
class Country extends \sky\yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'country';
    }
    
    public function behaviors() {
        return array_merge(parent::behaviors(), [
            TimestampBehavior::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'iso', 'iso3'], 'required'],
            [['currency_id', 'status', 'weight', 'created_at', 'updated_at'], 'integer'],
            [['name', 'phone_code'], 'string', 'max' => 255],
            [['iso'], 'string', 'max' => 2],
            [['iso3'], 'string', 'max' => 3],
            [['iso'], 'unique'],
            [['iso3'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'iso' => 'Iso',
            'iso3' => 'Iso3',
            'currency_id' => 'Currency ID',
            'phone_code' => 'Phone Code',
            'status' => 'Status',
            'weight' => 'Weight',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    
    public function getCurrency()
    {
        return $this->hasOne(Currency::class, ['id' => 'currency_id']);
    }
}
