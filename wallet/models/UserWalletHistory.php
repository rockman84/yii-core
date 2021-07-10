<?php

namespace sky\yii\wallet\models;

use Yii;
use sky\yii\db\ActiveRecord;
use sky\yii\wallet\models\UserWallet;
use sky\yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "user_wallet_history".
 *
 * @property int $id
 * @property int $user_wallet_id
 * @property int|null $ref_id
 * @property string|null $model_class
 * @property string|null $note
 * @property int $operators
 * @property float|null $value
 * @property float $old_wallet
 * @property float $new_wallet
 * @property int|null $created_at
 *
 * @property string $valueFormatted
 * @property string $oldWalletFormatted
 * @property string $newWalletFormatted
 * @property string $walletChanges
 * @property UserWallet $userWallet
 *
 * @property ActiveRecord $model
 */
class UserWalletHistory extends \sky\node\components\db\ActiveRecord
{
    const OPERATORS_ADD = 1;
    const OPERATORS_SUBTRACT = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_wallet_history';
    }
    
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ]);
    }
    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note', 'model_class', 'ref_id'], 'default', 'value' => null],
            [['user_wallet_id', 'operators'], 'required'],
            [['old_wallet'], 'default', 'value' => function (UserWalletHistory $model) {
                return $model->userWallet->value;
            }],
            [['user_wallet_id', 'ref_id', 'created_at', 'operators'], 'integer'],
            [['value'], 'number', 'min' => 0, 'max' => 10000000],
            [['value'], 'validateWallet'],
            [['model_class', 'note'], 'string', 'max' => 255],
            [['user_wallet_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserWallet::class, 'targetAttribute' => ['user_wallet_id' => 'id']],
        ];
    }

    public function validateWallet($attribute, $params = [])
    {
        $wallet = $this->userWallet;
        if (!$wallet) {
            return $this->addError('user_wallet_id', Yii::t('app', 'Wallet not found'));
        }
        if (!$wallet->isLastTransactionValid) {
            $this->addError('value', Yii::t('app', 'Wallet value not match transaction. Please contact admin!'));
        }
        if ($this->operators == static::OPERATORS_SUBTRACT && $this->value > $wallet->value) {
            return $this->addError('value', Yii::t('app', Yii::t('app', 'Balance not sufficient')));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_wallet_id' => Yii::t('app', 'User Wallet ID'),
            'ref_id' => Yii::t('app', 'Ref ID'),
            'model_class' => Yii::t('app', 'Model Class'),
            'note' => Yii::t('app', 'Note'),
            'value' => Yii::t('app', 'Value'),
            'created_at' => Yii::t('app', 'Created At'),
            'valueFormatted' => Yii::t('app', 'Value'),
            'oldWalletFormatted' => Yii::t('app', 'Before Wallet Value'),
            'newWalletFormatted' => Yii::t('app', 'After Wallet Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserWallet()
    {
        return $this->hasOne(UserWallet::class, ['id' => 'user_wallet_id']);
    }

    public function getModel()
    {
        return $this->hasOne($this->model_class, ['id' => 'ref_id']);
    }

    public function getValueFormatted()
    {
        return ($this->operators == static::OPERATORS_SUBTRACT ? '- ' : null) . UserWallet::formatValue($this->userWallet, $this->value);
    }

    public function getOldWalletFormatted()
    {
        return UserWallet::formatValue($this->userWallet, $this->old_wallet);
    }

    public function getNewWalletFormatted()
    {
        return UserWallet::formatValue($this->userWallet, $this->new_wallet);
    }

    public function getWalletChanges()
    {
        return $this->oldWalletFormatted . ' => ' . $this->newWalletFormatted;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $walletValue = $this->userWallet->value;
            if ($this->operators == static::OPERATORS_SUBTRACT) {
                $this->userWallet->value = $walletValue - $this->value;
            } elseif ($this->operators == static::OPERATORS_ADD) {
                $this->userWallet->value = $walletValue + $this->value;
            }
            $this->new_wallet = $this->userWallet->value;
        }
        return $insert && $this->userWallet->validate() && parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->userWallet->save();
        }
        return parent::afterSave($insert, $changedAttributes);
    }
}
