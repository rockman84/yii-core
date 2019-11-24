<?php
namespace sky\yii\helpers;

use yii\helpers\ArrayHelper;

class Inflector extends \yii\helpers\Inflector
{
    public static function replace($text, $params, $regex = '/\\{([\w\-\/\.]+)\\}/')
    {
        return preg_replace_callback($regex, function ($matches) use ($params) {
            $name = $matches[1];
            return ArrayHelper::getValue($params, $name, $name);
        }, $text);
    }
}