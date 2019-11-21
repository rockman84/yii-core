<?php

namespace sky\yii\models;

use Yii;
use app\components\helpers\CurrencyFormat;
use app\components\helpers\StringHelper;

/**
 * This is the model class for table "currency".
 *
 * @property int $id
 * @property string $name
 * @property string $iso3
 * @property string $symbol
 * @property string $rate
 * @property string $decimal
 * @property string $thousand_separator
 * @property int $status
 * @property int $weight
 * @property int $rate_updated_at
 * @property int $updated_at
 * @property int $created_at
 * 
 * @property string $format
 * @property string $sortFormat
 */
class Currency extends \app\components\db\BaseActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    
    public $value;
    
    public $prefix;
    
    public $suffix;
    
    static $_cache;
    
    const DEFAULT_CURRENCY_ISO = 'USD';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'iso3'], 'required'],
            [['rate', 'value'], 'number'],
            [['status', 'weight', 'rate_updated_at', 'updated_at', 'created_at'], 'integer'],
            [['name', 'iso3', 'symbol'], 'string', 'max' => 255],
            [['decimal', 'thousand_separator'], 'string', 'max' => 1],
            [['name'], 'unique'],
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
            'iso3' => 'Iso3',
            'symbol' => 'Symbol',
            'rate' => 'Rate',
            'decimal' => 'Decimal',
            'thousand_separator' => 'Thousand Separator',
            'status' => 'Status',
            'weight' => 'Weight',
            'rate_updated_at' => 'Rate Updated At',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
    
    public static function findByIso($iso, $cache = false)
    {
        if (!isset(static::$_cache[$iso]) || !$cache) {
            static::$_cache[$iso] = static::findOne(['iso3' => ucwords($iso)]);
        }
        return static::$_cache[$iso];
    }
    
    public function getFormat($decimals = 0, $template = '{prefix} {symbol} {value} {suffix}')
    {
        $value = number_format($this->value, $decimals, $this->decimal, $this->thousand_separator);
        return StringHelper::replace($template, $this->getParams($value));
    }
    
    public function getSortFormat($template = '{prefix} {symbol} {value} {suffix}')
    {
        $value = CurrencyFormat::shortValue($this->value);
        return StringHelper::replace($template, $this->getParams($value));
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
    
    public function setPrefix($text)
    {
        $this->prefix = $text;
        return $this;
    }
    
    public function setSuffix($text)
    {
        $this->suffix = $text;
        return $this;
    }
    
    public static function convert(Currency $isoForm, Currency $iso, $value)
    {
        if ($isoForm->id == $iso->id) {
            return $value;
        }
        return ($iso->rate / $isoForm->rate) * $value;
    }
    
    public static function getDefaultCurrency()
    {
        $model = static::findByIso(static::DEFAULT_CURRENCY_ISO);
        if (!$model) {
            throw new \yii\db\IntegrityException("Default Currency Not Found");
        }
        return $model;
    }
    
    /**
     * convert currency value
     * @param \app\models\Currency|string $iso
     * @param number $value
     * @return \app\models\Currency|null
     */
    public function convertTo($iso, $value = null)
    {
        if (($iso instanceof Currency) && $this->id == $iso->id) {
            $iso->value = $value;
            return $iso;
        }
        if (is_string($iso)) {
            if ($iso == $this->iso3) {
                return $this;
            }
            $iso = static::findByIso($iso);
        }        
        if ($iso) {
            $this->value = $value !== null ? $value : $this->value;
            $iso->value = static::convert($this, $iso, $this->value);
            return $iso;
        }
        return $this;
    }
    
    public function __toString() {
        return $this->convertTo(Yii::$app->user->currency)->getFormat();
    }
}
