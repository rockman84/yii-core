<?php

namespace sky\yii\models;

use Yii;
use sky\yii\helpers\Inflector;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "currency".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $symbol
 * @property number $rate
 * @property string $decimal_point
 * @property string $thousand_separator
 * @property string $prefix
 * @property string $suffix
 * @property int $status
 * @property int $weight
 * @property int $rate_updated_at
 * @property int $updated_at
 * @property int $created_at
 * 
 * @property string $format
 * @property string $sortFormat
 */
class Currency extends \sky\yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    
    public $value;
    
//    public $prefix;
//    
//    public $suffix;
    
    static $_cache;
    
    const DEFAULT_CURRENCY_CODE = 'USD';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency';
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
            [['name', 'code'], 'required'],
            [['rate', 'value'], 'number'],
            [['status', 'weight', 'rate_updated_at', 'updated_at', 'created_at'], 'integer'],
            [['name', 'symbol', 'prefix', 'suffix'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 3],
            [['decimal_point', 'thousand_separator'], 'string', 'max' => 1],
            [['name'], 'unique'],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Currency Name',
            'code' => 'Code',
            'symbol' => 'Symbol',
            'rate' => 'Rate',
            'decimal_point' => 'Decimal',
            'thousand_separator' => 'Thousand Separator',
            'status' => 'Status',
            'weight' => 'Weight',
            'rate_updated_at' => 'Rate Updated At',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
    
    public static function findByCode($code, $cache = false)
    {
        if (!isset(static::$_cache[$code]) || !$cache) {
            static::$_cache[$code] = static::findOne(['code' => ucwords($code)]);
        }
        return static::$_cache[$code];
    }
    
    public function getFormat($decimals = 0, $template = '{prefix} {value} {suffix}')
    {
        return $this->format($this->value, $decimals, $template);
    }
    
    public function format($value, $decimals = 0, $template = '{prefix} {value} {suffix}')
    {
        $format = number_format($value, $decimals, $this->decimal_point, $this->thousand_separator);
        return Inflector::replace($template, $this->getParams($format));
    }

    protected function getParams($value)
    {
        return array_merge($this->toArray(), ['value' => $value, 'prefix' => $this->prefix, 'suffix' => $this->suffix]);
    }
    
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    
    public static function convert(Currency $codeFrom, Currency $code, $value)
    {
        if ($codeFrom->id == $code->id) {
            return $value;
        }
        return ($code->rate / $codeFrom->rate) * $value;
    }
    
    public static function getDefaultCurrency()
    {
        $model = static::findByIso(static::DEFAULT_CURRENCY_CODE);
        if (!$model) {
            throw new \yii\db\IntegrityException("Default Currency Not Found");
        }
        return $model;
    }
    
    /**
     * convert currency value
     * @param \app\models\Currency|string $code
     * @param number $value
     * @return \app\models\Currency|null
     */
    public function convertTo($code, $value = null)
    {
        if (($code instanceof Currency) && $this->id == $code->id) {
            $code->value = $value;
            return $code;
        }
        if (is_string($code)) {
            if ($code == $this->code) {
                return $this;
            }
            $code = static::findByCode($code);
        }        
        if ($code) {
            $this->value = $value !== null ? $value : $this->value;
            $code->value = static::convert($this, $code, $this->value);
            return $code;
        }
        return $this;
    }
}
