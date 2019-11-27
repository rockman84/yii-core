<?php

namespace sky\yii\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\base\Model;

/**
 * This is the model class for table "tmp_form".
 *
 * @property int $id
 * @property string $key
 * @property int|null $user_id
 * @property resource $model_form
 * @property string|null $class_name
 * @property string $session
 * @property string $return_url
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property User $user
 * @property Model $model
 */
class TmpForm extends \sky\yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tmp_form';
    }
    
    public function behaviors() {
        return array_merge(parent::behaviors(), [
            TimestampBehavior::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            
            [['key'], 'default', 'value' => function () { return Yii::$app->security->generateRandomString(); }],
            [['user_id'], 'default', 'value' => function () { return Yii::$app->user->id; }],
            [['key', 'model_form'], 'required'],
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['model_form', 'session', 'url_return'], 'string'],
            [['key', 'class_name'], 'string', 'max' => 255],
            [['key'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }
    
    public function setModel(Model $model)
    {
        $this->model_form = serialize($model);
        $this->class_name = get_class($model);
    }
    
    public function getModel()
    {
        return unserialize($this->model_form);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'user_id' => 'User ID',
            'model_form' => 'Model Form',
            'class_name' => 'Class Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    public static function findByKey($key)
    {
        return static::findOne(['key' => $key]);
    }
    
    public static function create(Model $model, $validateModel = true, $options = [])
    {
        $tmp = new static($options);
        $tmp->setModel($model);
        return (!$validateModel || $model->validate()) && $tmp->save() ? $tmp : false;
    }
    
    public static function creates(Array $models, $validateModel = true, $options = [])
    {
        $options['session'] = Yii::$app->security->generateRandomString(10);
        $saved = true;
        foreach ($models as $model) {
            $saved = static::create($model, $validateModel, $options) && $saved;
        }
        return $saved ? $options['session'] : false;
    }
}
