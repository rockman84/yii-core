<?php
namespace sky\yii\models\form;

use sky\yii\models\User;
use Yii;
use yii\db\ActiveRecord;

class Signup extends \yii\base\Model
{

    public $email;
    
    public $password;

    public $confirmPassword;

    public $verifyCode;

    protected $_user;

    public function rules() {
        return [
            [['email', 'password', 'verifyCode'], 'required'],
            ['email', 'email'],
            [['confirmPassword', 'password'], 'string', 'min' => 6],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password'],
            ['email', 'trim'],
            [['email'], 'filter', 'filter' => 'strtolower'],
        ];
    }
    
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $user = $this->getUser();
        $user->load($this->getAttributes(), '');
        $user->setPassword($this->password);
        if ($user->save()) {
            try {
                static::getUserWeb()->login($user);
            } catch (\Exception $e) {
                Yii::error($e->getMessage());
            }
            return $user;
        }
        $this->addErrors($user->errors);
    }

    /**
     * @return ActiveRecord
     * @throws \yii\base\InvalidConfigException
     */
    public function getUser()
    {
        if (!$this->_user) {
            $this->_user = static::getUserClass();
        }
        return $this->_user;
    }

    public static function getUserWeb()
    {
        return Yii::$app->user;
    }

    public static function getUserClass()
    {
        return Yii::createObject(static::getUserWeb()->identityClass);
    }
}