<?php
namespace sky\yii\web;

use Yii;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\FormatConverter;
use sky\yii\models\Currency;
use kartik\select2\Select2;
use kartik\number\NumberControl;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use kartik\typeahead\Typeahead;
use kartik\rating\StarRating;
use vova07\imperavi\Widget;
use kartik\switchinput\SwitchInput;

use trntv\aceeditor\AceEditor;
use yii\captcha\Captcha;
use unclead\multipleinput\MultipleInput;

class ActiveField extends \yii\bootstrap4\ActiveField
{
    
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
    
    public function aceEditor($config = [])
    {
        return $this->widget(AceEditor::class, ArrayHelper::merge([
            'theme' => 'github',
        ], $config));
    }
    
    /**
     * @see https://github.com/vova07/yii2-imperavi-widget
     * @param type $config
     * @return type
     */
    public function redactor($config = [])
    {
        return $this->widget(Widget::class, ArrayHelper::merge([
            'settings' => [
                'minHeight' => 600,
                'plugins' => [
                    'clips',
                    'fullscreen',
                    'imagemanager',
                ],
            ],
            
        ], $config));
    }
    
    public function captcha($config = [])
    {
        return $this->widget(Captcha::class, array_merge([
            'options' => [
                'class' => 'form-control mt-2',
                'placeholder' => Yii::t('app', 'Type the text'),
            ],
        ], $config))->hint(Yii::t('app', 'Click Image to reload image'));
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
    
    public function select2($config = [])
    {
        return $this->widget(Select2::class, ArrayHelper::merge([
            'options' => ['placeholder' => 'Select...']
        ], $config));
    }

    public function starRating($config = [])
    {
        return $this->widget(StarRating::class, ArrayHelper::merge([
            'pluginOptions' => [
                'step' => 1,
                'stars' => 5,
            ]
        ], $config));
    }

    public function switchInput($config = [])
    {
        return $this->widget(SwitchInput::class, ArrayHelper::merge([

        ], $config));
    }
    
    public function typeahead($options)
    {
        return $this->widget(Typeahead::class, ArrayHelper::merge([], $options));
    }
    
    public function multipleInput($config)
    {
        return $this->widget(MultipleInput::class, ArrayHelper::merge([
            'iconSource' => MultipleInput::ICONS_SOURCE_FONTAWESOME,
            'max' => 20,
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