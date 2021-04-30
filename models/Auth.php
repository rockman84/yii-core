<?php

namespace sky\yii\models;

use sky\yii\db\ActiveRecord;
use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "auth".
 * https://github.com/yiisoft/yii2-authclient/tree/master/docs/guide
 *
 * @property int $id
 * @property int $user_id
 * @property string $source
 * @property string $source_id
 * @property string|null $data
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property User $user
 */
class Auth extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth';
    }
    
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
        ]);
    }
    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'source', 'source_id'], 'required'],
            [['source'], 'unique', 'targetAttribute' => ['source', 'source_id']],
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['data'], 'safe'],
            [['source', 'source_id'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'source' => Yii::t('app', 'Source'),
            'source_id' => Yii::t('app', 'Source ID'),
            'data' => Yii::t('app', 'Data'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
