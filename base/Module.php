<?php
namespace sky\yii\base;

use yii\helpers\Inflector;
use Yii;

class Module extends \yii\base\Module
{
    public $modulesNode = [];
    
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
        $url[0] = '/' . $this->id . '/' . $url[0];
        return Yii::$app->urlManager->createUrl($url);
    }
    
    public function getModuleStructure(Module $module = null)
    {
        $module = $module ? : $this;
        if ($module instanceof Module) {
            $this->modulesNode[] = $module->id;
            if ($module->module instanceof Module) {
                return $this->getModuleIds($module->module);
            }
        }
        return $this->modulesNode;
    }
}