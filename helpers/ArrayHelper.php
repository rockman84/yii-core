<?php
namespace sky\yii\helpers;

use yii\helpers\Json;
use Yii;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * ```
     * $object = [
     *      'A' => 1
     *      'B' => [
     *          'B1' => true,
     *          'B2' => 3,
     *      ]
     * ];
     *
     * $mappingAttribute = [
     *      'id' => 'A',
     *      'value_1' => 'B.B2',
     *      'value_2' => 'B.B1:boolean'
     * ];
     *
     * // $result will be:
     * // [
     * //   'id' => 1,
     * //   'value_1' => 3,
     * //   'value_2' => 'Yes',
     * // ]
     * static::getArrayMapValue($object, $mappingAttribute);
     * ```
     *
     * @param $array
     * @param $mappingAttribute
     * @return array
     * @throws \Exception
     */
    public static function getArrayMapValue($array, $mappingAttribute)
    {
        $data = [];
        foreach ($mappingAttribute as $attribute => $key) {
            if (is_array($key)) {
                $value = static::getArrayMapValue($key, $array);
            } elseif (is_string($key)) {
                $part = explode(':', $key);
                $attrValue = static::getValue($array, $part[0]);
                $value = isset($part[1]) ? Yii::$app->formatter->{'as' . ucfirst($part[1])}($attrValue) : $attrValue;
                if (is_int($attribute)) {
                    $attribute = $part[0];
                }
            } else {
                $value = static::getValue($array, $key);
            }
            $data[$attribute] = $value;
        }
        return $data;
    }
}