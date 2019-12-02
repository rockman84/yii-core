<?php
namespace sky\yii\grid;

use yii\bootstrap4\Html;

class LinkColumn extends \yii\grid\DataColumn
{
    public $url = null;
    
    public $linkOptions = [];
    
    public function getDataCellValue($model, $key, $index) {
        
        return $this->renderLink(parent::getDataCellValue($model, $key, $index));
    }
    
    public function renderLink($text)
    {
        if ($this->url === null) {
            return $text;
        }
        if (is_callable($this->url)) {
            $this->url = call_user_func($this->url, $model, $key, $index);
        }
        $this->format = 'raw';
        return Html::a($text, $this->url, $this->linkOptions);
    }
    
}