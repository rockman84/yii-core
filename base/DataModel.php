<?php
namespace sky\yii\base;

use Google\Service\Analytics\Resource\Data;
use yii\base\Model;

/**
 *
 * @property-read array $data
 */
class DataModel extends Model
{
    protected array $_relationAttributes = [];

    public function __get($name)
    {
        if (key_exists($name, $this->_relationAttributes)) {
            return $this->_relationAttributes[$name];
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        $relAttributes = $this->relationAttributes();
        if (key_exists($name)) {
            if (is_array($relAttributes[$name])) {

            }
        }
        return parent::__set($name, $value);
    }

    public function init()
    {
        parent::init();
        foreach ($this->relationAttributes() as $attribute => $type) {
            $this->_relationAttributes[$attribute] = is_array($type) ? [] : null;
        }
    }

    public function relationAttributes(): array
    {
        return [
            'child' => [Data::class],
            'info' => Data::class,
        ];
    }

    public function getData(): array
    {
        return $this->attributes;
    }
}