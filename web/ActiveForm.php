<?php
namespace sky\yii\web;

class ActiveForm extends \yii\bootstrap4\ActiveForm
{
    public $fieldClass = 'sky\yii\web\ActiveField';
    
    /**
     * {@inheritdoc}
     * @return ActiveField the created ActiveField object
     */
    public function field($model, $attribute, $options = array()) {
        return parent::field($model, $attribute, $options);
    }
}