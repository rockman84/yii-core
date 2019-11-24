<?php

namespace sky\yii\models;

use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $auth_key
 * @property string $email
 * @property string $email_verification
 * @property string $access_token
 * @property int $token_expiry
 * @property string $ip_address
 * @property string $user_agent
 * @property string $password_hash
 * @property string $password_reset_token
 * @property int $login_attempt
 * @property int $type
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property boolean #isGod
 * @property boolean $isAdmin
 * @property boolean $isOwner
 * @property boolean $isActive
 * @property boolean $isAccessTokenExpired
 */
class User extends \sky\yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const PASSWORD_NOTSET = 'PASSWORD NOT SET';
    
    const STATUS_ACTIVE = 1;
    const STATUS_NOT_VERIFICATION = 10;
    
    const TYPE_NORMAL = 1;
    const TYPE_ADMIN = 99;
    const TYPE_GOD = 100;
    
    const ACTION_CREATE_CAMPAIGN = 'createCampaign';
    const ACTION_CREATE_AGENT = 'createAgent';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
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
            [['email', 'password_hash'], 'required'],
            [['email'], 'filter', 'filter' => function ($value) {
                return strtolower($value);
            }],
            [['email'], 'email'],
            [['access_token', 'user_agent'], 'string', 'max' => 255],
            [['token_expiry'], 'integer'],
            [['login_attempt', 'type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['email', 'email_verification', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => static::STATUS_NOT_VERIFICATION],
            [['type'], 'default', 'value' => static::TYPE_NORMAL],
            [['username', 'email', 'access_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'email_verification' => 'Email Verification',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'login_attempt' => 'Login Attempt',
            'type' => 'User Type',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
    }
    
    public function getUserReward()
    {
        return $this->hasMany(UserReward::class, ['user_id' => 'id']);
    }
    
    public function getActiveUserReward()
    {
        return $this->getUserReward()->andWhere(['status' => UserReward::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if ($token) {
            $user = static::findOne(['access_token' => $token]);
            if ($user && !$user->isAccessTokenExpired) {
                return $user;
            }
        }
        return null;
    }
    
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
        $this->token_expiry = time() + 7600;
        return $this;
    }
    
    public function getIsAccessTokenExpired()
    {
        return $this->token_expiry < time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
    
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }
    
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = time() . '-' . Yii::$app->security->generateRandomString();
    }
    
    public function generateEmailVerification()
    {
        $this->email_verification = Yii::$app->security->generateRandomString();
    }
    
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }    

    public function login($duration = 0, $slack = true)
    {
        $isLogin = Yii::$app->user->login($this, $duration);
        if ($isLogin) {
            $this->login_attempt = 0;
            $this->user_agent = substr(Yii::$app->request->userAgent, 0, 250);
            $this->ip_address = Yii::$app->request->remoteIP;
            $this->save();
        }
        return $isLogin;
    }
    
    public function getIsGod()
    {
        return $this->status == static::TYPE_GOD;
    }
    
    public function getIsAdmin()
    {
        return in_array($this->type, [static::TYPE_ADMIN, static::TYPE_GOD]);
    }
    
    public function getIsOwner()
    {
        return $this->id == Yii::$app->user->id;
    }
    
    public function getIsActive()
    {
        return $this->status == static::STATUS_ACTIVE;
    }
    
    
}
