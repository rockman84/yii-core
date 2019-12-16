<?php
namespace sky\yii\web;

use yii\helpers\ArrayHelper;
use sky\yii\models\Currency;
use yii\bootstrap4\Html;
use kartik\select2\Select2;
use kartik\number\NumberControl;
use kartik\date\DatePicker;
use unclead\multipleinput\MultipleInput;


class ActiveField extends \yii\bootstrap4\ActiveField
{
    const DATE_PICKER_DEFAULT_FORMAT = 'dd M yyyy';
    
    public function numberInput($config = [])
    {
        return $this->widget(NumberControl::class, $config);
    }
    
    public function currencyMoneyInput($currency = 'currency', $config = [])
    {
        $currency = $currency instanceof Currency ? $currency : $this->model->{$currency};
        return $this->numberInput(ArrayHelper::merge([
            'maskedInputOptions' => [
                'prefix' => $currency->prefix ? $currency->prefix . ' ' : '',
                'suffix' => $currency->suffix ? ' ' . $currency->suffix : '',
                'digits' => 2,
                'groupSeparator' => $currency->thousand_separator,
                'radixPoint' => $currency->decimal_point,
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
    
    public function dateRange($config = [])
    {
        return $this->widget(DateRangePicker::class, ArrayHelper::merge([], $config));
    }
    
    public function select2($config = [])
    {
        return $this->widget(Select2::class, ArrayHelper::merge([], $config));
    }
    
    public function multipleInput($config)
    {
        return $this->widget(MultipleInput::class, $config);
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