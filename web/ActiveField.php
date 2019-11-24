<?php
namespace sky\yii\web;

use yii\helpers\ArrayHelper;
use app\models\Currency;
use yii\bootstrap4\Html;
use kartik\select2\Select2;
use kartik\money\MaskMoney;
use kartik\date\DatePicker;

class ActiveField extends \yii\bootstrap4\ActiveField
{
    const DATE_PICKER_DEFAULT_FORMAT = 'dd M yyyy';
    
    public function moneyInput($config = [])
    {
        return $this->widget(MaskMoney::class, $config);
    }
    
    public function currencyMoneyInput($currency = 'currency', $config = [])
    {
        $currency = $currency instanceof Currency ? $currency : $this->model->{$currency};
        return $this->moneyInput(ArrayHelper::merge([
            'pluginOptions' => [
                'thousands' => $currency->thousand_separator,
                'decimal' => $currency->decimal,
                'prefix' => $currency->prefix ? : $currency->symbol . ' ',
                'suffix' => $currency->suffix ? : '',
                'allowEmpty' => false,
                'allowNegative' => false,
                'precision' => 0
            ],
        ], $config));
    }
    
    public function datePicker($config = [])
    {
        return $this->widget(DatePicker::class, array_merge([
            'readonly' => true,
            'language' => 'en',
            'pluginOptions' => [
                'format' => static::DATE_PICKER_DEFAULT_FORMAT,
            ],
        ], $config));
    }
    
    public function label($label = null, $options = [])
    {
        if ($label !== false && $this->model->isAttributeRequired($this->attribute)) {
            if ($label === null) {
                $label = $this->model->getAttributeLabel(Html::getAttributeName($this->attribute));
            }
            $label = $label . ' ' . Html::tag('span', '*', ['class' => 'req']);
        }
        return parent::label($label, $options);
    }
    
    protected function isRule($class)
    {
        foreach ($this->model->getActiveValidators($this->attribute) as $rule) {
            if ($rule instanceof $class) {
                return true;
            }
        }
        return false;
    }
}