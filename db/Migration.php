<?php
namespace sky\yii\db;

use yii\helpers\ArrayHelper;

class Migration extends \yii\db\Migration
{
    public function createNewTable($table, $columns, $options = null) {
        $columns = ArrayHelper::merge($this->defaultColumns(), $columns);
        return $this->createTable($table, $columns, $options);
    }
    
    public function defaultColumns()
    {
        return [
            'created_at' => $this->integer()->unsigned()->defaultValue(null),
            'updated_at' => $this->integer()->unsigned()->defaultValue(null),
            'created_by' => $this->integer()->defaultValue(null),
            'updated_by' => $this->integer()->defaultValue(null),
        ];
    }
}