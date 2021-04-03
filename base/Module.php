<?php
namespace sky\yii\base;

use yii\helpers\Inflector;
use Yii;

/**
 * Class SkyNode Module
 * @package sky\yii\base
 */
class Module extends \yii\base\Module
{
    /**
     * @var null
     */
    public $parentIds = null;

    /**
     * @var null
     */
    public $baseUrl = null;

    /**
     * @inheritDoc
     */
    public function init() {
        
        if ($this->baseUrl === null) {
            if ($this->parentIds === null) {
                static::getParentModuleIds($this, $this->parentIds);
            }
            $this->baseUrl = implode( '/', array_reverse($this->parentIds));
        }
        
        return parent::init();
    }

    /**
     * Module Name
     * @return string
     */
    public function name()
    {
        return Inflector::humanize($this->id);
    }

    /**
     * Module Description
     * @return string
     */
    public function description()
    {
        return null;
    }

    /**
     * Create Relative URL by Module
     * @param $url
     * @return string
     */
    public function createUrl($url)
    {
        $url[0] = '/' . $this->baseUrl . '/' . $url[0];
        return Yii::$app->urlManager->createUrl($url);
    }

    /**
     * Get Parent Module by IDs
     * @param Module $module
     * @param $ids
     * @return mixed
     */
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