<?php
namespace sky\yii\models\form;

use sky\yii\models\User;

class Signup extends \yii\base\Model
{
    public $email;
    
    public $password;
    
    public function rules() {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['password', 'string', 'min' => 6],
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
            $user = new User;
            $user->load($this->getAttributes(), '');
            $user->setPassword($this->password);
            return $user->save() ? $user : false;
        }
        return false;
    }
}