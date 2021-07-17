<?php
namespace sky\yii\models\form;

use sky\yii\models\User;
use Yii;

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
            [['email'], 'checkEmail'],
            [['email'], 'filter', 'filter' => 'strtolower'],
        ];
    }
    
    public function checkEmail($attribute, $params)
    {
        $user = User::findOne(['email' => $this->{$attribute}]);
        if ($user) {
            return $this->addError($attribute, 'Email sudah terdaftar.');
        }
    }
    
    public function save()
    {

        if ($this->validate()) {
            $user = $this->getUser();
            $user->load($this->getAttributes(), '');
            $user->setPassword($this->password);
            if ($user->save()) {
                Yii::$app->user->login($user);
                return $user;
            }
        }
        return false;
    }

    /**
     * @return User
     * @throws \yii\base\InvalidConfigException
     */
    public function getUser()
    {
        if (!$this->_user) {
            $this->_user = Yii::createObject(Yii::$app->user->identityClass);
        }
        return $this->_user;
    }
}