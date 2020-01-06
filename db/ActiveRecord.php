<?php
namespace sky\yii\db;

use yii\db\ActiveQuery;
use yii\base\ModelEvent;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use app\models\User;
use Yii;

/**
 * @property array $accessErrors
 */
class ActiveRecord extends \yii\db\ActiveRecord
{    
    const ACTION_VIEW = 'view';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_HARD_DELETE = 'hardDelete';
    
    const EVENT_BEFORE_ACCESS = 'beforeAccess';
    
    protected $enableDelete = false;
    protected $accessError = [];
    protected $access = [];

    public function __get($name) {
        $dataProperty = $this->propertyAttributes();
        if (isset($dataProperty[$name])) {
            return ArrayHelper::getValue($this, $dataProperty[$name]);
        }
        return parent::__get($name);
    }
    
    public function __set($name, $value) {
        $dataProperty = $this->propertyAttributes();
        if (isset($dataProperty[$name])) {
            $path = explode('.', $dataProperty[$name]);
            $name = array_shift($path);
            $data = [];
            ArrayHelper::setValue($data, implode('.', $path), $value);
            $value = array_merge($this->{$name} ? : [], $data);
        }
        return parent::__set($name, $value);
    }
    
    public function getCreator($attribute = 'created_by')
    {
        if ($this->hasAttribute($attribute)) {
            return $this->hasOne(User::className(), ['id' => $attribute]);
        }
        return false;
    }
    
    public function getUpdater($attribute = 'updated_by')
    {
        if ($this->hasAttribute($attribute)) {
            return $this->hasOne(User::className(), ['id' => $attribute]);
        }
        return false;
    }
    
    public static function getConstants($name) 
    {
        $self = new \ReflectionClass(new static());
        $contants = $self->getConstants();
        $prefix = strtoupper($name) . '_';
        $prefixLength = strlen($prefix);
        $prefixOffset = $prefixLength - 1;
        $options = [];
        foreach ($contants as $key => $value) {
            if (substr($key, 0, $prefixLength) === $prefix) {
                $options[$value] = ucwords(strtolower(Inflector::humanize(substr($key, $prefixLength))));
            }
        }
        
        if (!$options) {
            return false;
        } else {
            return $options;
        }
    }
    
    public static function getConstant($name, $value)
    {
        if ($options = static::getConstants($name)) {
            if (isset($options[$value])) {
                return $options[$value];
            }
        }
        return false;
    }
    
    public static function getOptions($to = 'name', $from = 'id', $group = null, $query = null) {

        $queryBase = static::find();
        if (is_callable($query)) {
            call_user_func($query, $queryBase);
        } elseif ($query instanceof ActiveQuery) {
            $queryBase = $query;
        }
        return ArrayHelper::map($queryBase->all(), $from, $to, $group);
    }
    
    public function propertyAttributes()
    {
        return [];
    }
    
    public function hasPropertyAttribute($name)
    {
        $property = $this->propertyAttributes();
        return isset($property[$name]);
    }

    public function can($action, User $user = null, $exception = false)
    {
        $user = $user instanceof User ? $user : (!Yii::$app->user->isGuest ? Yii::$app->user->identity : null);
        $key = $action . '-' . ($user ? $user->id : 'guest');
        if (!isset($this->access[$key]) && ($beforeAccess = $this->beforeAccess($action, $user))) {
            $action = 'access' . lcfirst($action);
            if (method_exists($this, $action)) {
                $this->access[$key] = $this->{$action}($user);
            } else {
                throw new \yii\base\InvalidArgumentException('unknown action access ' . $action);
            }
        }
        if (!isset($this->access[$key])) {
            $this->access[$key] = false;
        }
        
        if ($exception && !$this->access[$key]) {
            throw new \yii\web\UnauthorizedHttpException('Akses ditolak');
        }
        return $this->access[$key];
    }
    
    public function beforeAccess($action, $user)
    {
        $this->trigger(static::EVENT_BEFORE_ACCESS, new ModelEvent());
        return true;
    }
    
    public function getValue($key)
    {
        return ArrayHelper::getValue($this, $key);
    }
    
    public function addAccessError($action, $message)
    {
        $this->accessError[$action] = $message;
    }
    
    public function getAccessErrors($type = null)
    {
        if ($type == null) {
            return $this->accessError;
        }
        return ArrayHelper::getValue($this->accessError, $type);
    }
    
    public function load($data = false, $formName = null) {
        if ($data === false) {
            $data = Yii::$app->request->post();
        }
        return parent::load($data, $formName);
    }
    
    public function afterValidate() {
        return parent::afterValidate();
    }
    
    public static function findLast()
    {
        return static::find()->orderBy(['id' => SORT_DESC])->one();
    }
}