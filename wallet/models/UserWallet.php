<?php

namespace sky\yii\wallet\models;


use Yii;
use yii\base\InvalidCallException;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use sky\yii\models\User;
use sky\yii\models\Currency;
use sky\yii\db\ActiveRecord;
use sky\yii\wallet\models\UserWalletHistory;

/**
 * This is the model class for table "user_wallet".
 *
 * @property int $id
 * @property string|null $key_id
 * @property int $user_id
 * @property int $currency_id
 * @property float|null $value
 * @property int|null $expire_at
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Currency $currency
 * @property string $valueFormatted
 * @property User $user
 * @property UserWalletHistory[] $userWalletHistories
 * @property-read UserWalletHistory $lastTransactionHistory
 * @property-read bool $isLastTransactionValid
 */
class UserWallet extends \sky\node\components\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_wallet';
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
            [['key_id'], 'default', 'value' => function (UserWallet $model) {
                return Yii::$app->security->generateSerial();
            }],
            [['user_id', 'currency_id'], 'required'],
            [['user_id', 'currency_id', 'expire_at', 'created_at', 'updated_at'], 'integer'],
            [['currency_id'], 'unique', 'targetAttribute' => ['user_id', 'currency_id']],
            [['value'], 'number'],
            [['key_id'], 'string', 'max' => 255],
            [['key_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['currency_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'key_id' => Yii::t('app', 'Key ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'currency' => Yii::t('app', 'Currency'),
            'value' => Yii::t('app', 'Value'),
            'expire_at' => Yii::t('app', 'Expire At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'valueFormatted' => Yii::t('app', 'Credit'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::class, ['id' => 'currency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserWalletHistories()
    {
        return $this->hasMany(UserWalletHistory::class, ['user_wallet_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastTransactionHistory()
    {
        return $this->hasOne(UserWalletHistory::class, ['user_wallet_id' => 'id'])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC]);
    }

    public function getIsLastTransactionValid()
    {
        if ($this->lastTransactionHistory) {
            return $this->lastTransactionHistory->new_wallet == $this->value;
        }
        return $this->value == 0;
    }

    public function checkBalance($value)
    {
        return $this->value >= $value;
    }

    public function createTransaction ($operator, $value, $note = null, ActiveRecord $refModel = null)
    {
        if ($this->isNewRecord && !$this->save()) {
            throw new InvalidCallException("Fail Create User Wallet");
        }
        $walletHistory = new UserWalletHistory([
            'user_wallet_id' => $this->id,
            'value' => $value,
            'operators' => $operator,
            'note' => $note
        ]);
        if ($refModel) {
            $walletHistory->model_class = get_class($refModel);
            $walletHistory->ref_id = $refModel->id;
        }
        return $walletHistory;
    }

    public function getValueFormatted()
    {
        return static::formatValue($this, $this->value);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->value = 0;
        }
        return parent::beforeSave($insert);
    }

    public static function formatValue(UserWallet $wallet, $value, $decimals = 0)
    {
        return $wallet->currency->format($value, $decimals);
    }

    public static function getOrCreate(\sky\yii\models\User $user, Currency $currency)
    {
        $wallet = static::findOne(['currency_id' => $currency->id, 'user_id' => $user->id]);
        if (!$wallet) {
            $wallet = new static([
                'user_id' => $user->id,
                'currency_id' => $currency->id,
                'value' => 0,
            ]);
            $wallet->save();
        }
        return $wallet;
    }

    /**
     * @param UserWallet $from
     * @param UserWallet $to
     * @param $value
     * @param string|null $note
     * @return bool|void
     */
    public static function transfer(UserWallet $from, UserWallet $to, $value, $note = null)
    {
        if ($value < 1 || $from->currency_id != $to->currency_id) {
            return false;
        }
        $fromModel = $from->createTransaction(
            UserWalletHistory::OPERATORS_SUBTRACT,
            $value,
            $note,
            $to
        );
        $toModel = $to->createTransaction(
            UserWalletHistory::OPERATORS_ADD,
            $value,
            $note,
            $from
        );
        if ($fromModel->validate() && $toModel->validate()) {
            return $fromModel->save() && $toModel->save();
        }
        return [$fromModel->errors, $toModel->errors];
    }
}
