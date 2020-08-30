<?php
namespace sky\yii\web;

use yii\helpers\Json;

/**
 * @property string $jForm
 */
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
    
    /**
     * https://www.yiiframework.com/doc/guide/2.0/en/input-form-javascript
     * @param array|string $ajaxOptions
     */
    public function ajaxSubmit($ajaxOptions = '{}')
    {
        if (is_array($ajaxOptions)) {
            $options = Json::encode($ajaxOptions);
        } else {
            $options = $ajaxOptions;
        }
        
        $this->view->registerJs(<<<JS
            var form = {$this->jForm};
            form.on('beforeSubmit', function (event) {
                var opts = $.extend({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function (data, status, xhr) {
                            form.html(status);
                        },
                        error: function (d) {
                            console.log(d);
                            console.log('Fail Submit');
                        }
                    },
                    {$options});
                $.ajax(opts);
                return false;
            });
JS
        );
    }
    /**
     * get js jquery form instance
     * @return string
     */
    public function getJForm()
    {
        return "$('#{$this->id}')";
    }
    
}