<?php
namespace sky\yii\base;

use yii\helpers\Inflector;

class Module extends \yii\base\Module
{
    public function getName()
    {
        return Inflector::humanize($this->id);
    }
    
    public function getDescription()
    {
        return;
    }
}