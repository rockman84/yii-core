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
 * @property string $email_verification_token
 * @property string $auth_key
 * @property string $ip_address
 * @property string $user_agent
 * @property string $password_hash
 * @property string $password_reset_token
 * @property int $login_attempt
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property boolean $isOwner
 * @property boolean $isActive
 * @property TmpForm[] $tmpForm
 */
class User extends \sky\yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const PASSWORD_NOTSET = 'PASSWORD NOT SET';
    
    const STATUS_ACTIVE = 1;
    const STATUS_NOT_VERIFICATION = 10;
    const STATUS_INACTIVE = 0;

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
            [['email'], 'filter', 'filter' => 'strtolower'],
            [['email'], 'email'],
            [['user_agent'], 'string', 'max' => 255],
            [['login_attempt', 'status', 'created_at', 'updated_at'], 'integer'],
            [['email', 'email_verification_token', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => static::STATUS_NOT_VERIFICATION],
            [['email', 'auth_key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'email_verification_token' => 'Email Verification',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'login_attempt' => 'Login Attempt',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    
    public function fields() {
        return [
            'id',
            'email',
            'status',
            'created_at',
            'updated_at',
        ];
    }
    
    public function getTmpForm()
    {
        return $this->hasMany(TmpForm::class, ['user_id' => 'id']);
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
        return $token;
        if ($token) {
            $user = static::findOne(['auth_key' => $token]);
            if ($user && !$user->isAccessTokenExpired) {
                return $user;
            }
        }
        return null;
    }
    
    public function generateAccessToken()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
        return $this;
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
        $this->email_verification_token = Yii::$app->security->generateRandomString();
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
    
    public function getIsOwner()
    {
        return $this->id == Yii::$app->user->id;
    }
    
    public function getIsActive()
    {
        return $this->status == static::STATUS_ACTIVE;
    }
    
    
}
