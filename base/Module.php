<?php
namespace sky\yii\base;

use yii\helpers\Inflector;
use Yii;

class Module extends \yii\base\Module
{
    public $parentIds = null;
    
    public $baseUrl = null;
    
    public function init() {
        
        if ($this->baseUrl === null) {
            if ($this->parentIds === null) {
                static::getParentModuleIds($this, $this->parentIds);
            }
            $this->baseUrl = implode( '/', array_reverse($this->parentIds),);
        }
        
        return parent::init();
    }
    
    public function name()
    {
        return Inflector::humanize($this->id);
    }
    
    public function description()
    {
        return;
    }
    
    public function createUrl($url)
    {
        $url[0] = '/' . $this->baseUrl . '/' . $url[0];
        return Yii::$app->urlManager->createUrl($url);
    }
    
    public static function getParentModuleIds(Module $module, &$ids)
    {
        if ($module instanceof Module) {
            $ids[] = $module->id;
            if ($module->module instanceof Module) {
                return static::getParentModuleIds($module->module, $ids);
            }
        }
    }
}