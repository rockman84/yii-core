<?php
namespace sky\yii\web;

use Yii;
use yii\helpers\ArrayHelper;
use sky\yii\models\Currency;
use yii\bootstrap4\Html;
use kartik\select2\Select2;
use kartik\number\NumberControl;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use unclead\multipleinput\MultipleInput;
use kartik\typeahead\Typeahead;
use yii\helpers\FormatConverter;


class ActiveField extends \yii\bootstrap4\ActiveField
{
    const DATE_PICKER_DEFAULT_FORMAT = 'dd MMM yyyy';
    const DATE_PICKER_DEFAULT_PHP_FORMAT = 'd M Y';
    
    const DATETIME_PICKER_DEFAULT_BOOTSTRAP_FORMAT = 'dd M yyyy HH:ii:ss';
    const DATETIME_PICKER_DEFAULT_PHP_FORMAT = 'd M Y H:i:s';
    
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
        
        return $this->widget(DatePicker::class, ArrayHelper::merge([
            'readonly' => true,
            'language' => 'en',
            'convertFormat' => true,
            'pluginOptions' => [
                'autoClose' => true,
                'orientation' => 'bottom left right',
            ],
        ], $config));
    }
    
    public function dateTimePicker($config = [])
    {
        return $this->widget(DateTimePicker::class, array_merge([
            'readonly' => true,
            'language' => 'en',
            'convertFormat' => true,
            'pluginOptions' => [
                'autoClose' => true,
                'orientation' => 'bottom left right',
            ],
        ], $config));
    }
    
    public function dateRange($config = [])
    {
        return $this->widget(DateRangePicker::class, ArrayHelper::merge([], $config));
    }
    
    public function select2($config = [])
    {
        return $this->widget(Select2::class, ArrayHelper::merge([
            'options' => ['placeholder' => 'Select...']
        ], $config));
    }
    
    public function typeahead($options)
    {
        return $this->widget(Typeahead::class, ArrayHelper::merge([

        ], $options));
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