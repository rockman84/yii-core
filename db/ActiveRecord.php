<?php
namespace sky\yii\db;

use yii\db\ActiveQuery;
use yii\base\ModelEvent;
use sky\yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use app\models\User;
use Yii;

/**
 * @property array $accessErrors
 * @property array $constantOptions
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

    static $_constantOptions = [];

    public static function getConstantOptions()
    {
        $className = static::class;
        if (!array_key_exists($className, static::$_constantOptions)) {
            $self = new \ReflectionClass(new static());
            $labels = static::constantLabels();

            $constants = [];
            foreach ($self->getConstants() as $key => $value) {
                $constants[$key] = [
                    'label' => ArrayHelper::getValue($labels, $key, null),
                    'value' => $value,
                ];
            }
            static::$_constantOptions[$className] = $constants;
        }
        return static::$_constantOptions[$className];
    }

    /**
     * @param $name
     * @return array|false
     */
    public static function getConstants($name)
    {
        $contants = static::getConstantOptions();
        $prefix = strtoupper($name) . '_';
        $prefixLength = strlen($prefix);
        $prefixOffset = $prefixLength - 1;
        $options = [];
        foreach ($contants as $key => $const) {
            if (substr($key, 0, $prefixLength) === $prefix) {
                if ($const['label'] === null) {
                    $const['label'] = Yii::t('app', ucwords(strtolower(Inflector::humanize(substr($key, $prefixLength)))));
                    static::$_constantOptions[static::class][$key]['label'] = $const['label'];
                }
                $options[$const['value']] = $const['label'] ;
            }
        }
        return $options;
    }

    /**
     * @param $name
     * @param $value
     * @return false|mixed
     */
    public static function getConstant($name, $value)
    {
        if ($options = static::getConstants($name)) {
            if (isset($options[$value])) {
                return $options[$value];
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public static function constantLabels()
    {
        return [];
    }

    public function getCreator($attribute = 'created_by')
    {
        if ($this->hasAttribute($attribute)) {
            return $this->hasOne(User::className(), ['id' => $attribute]);
        }
        return false;
    }

    public function addError($attribute, $error = '')
    {
        Yii::error($attribute . ': ' . $error, 'validation');
        return parent::addError($attribute, $error);
    }

    public function getUpdater($attribute = 'updated_by')
    {
        if ($this->hasAttribute($attribute)) {
            return $this->hasOne(User::className(), ['id' => $attribute]);
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

    /**
     * @deprecated april 2021
     */
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

    /**
     * @deprecated april 2021
     * @param $action
     * @param $message
     */
    public function addAccessError($action, $message)
    {
        $this->accessError[$action] = $message;
    }

    /**
     * @deprecated april 2021
     * @param null $type
     * @return array|mixed
     * @throws \Exception
     */
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
    

    public static function findLast()
    {
        return static::find()->orderBy(['id' => SORT_DESC])->one();
    }

    protected static function store(): ?\yii\caching\CacheInterface
    {
        return Yii::$app->cache;
    }

    public function getPrefixIdStore(): string
    {
        return static::tableName() . '-' . $this->getPrimaryKey();
    }

    public function setStore($key, $value, $duration = 3600, $dependency = null)
    {
        static::store()->set($this->getPrefixIdStore() . '-' . $key, $value, $duration, $dependency);
    }

    public function getStore($key)
    {
        static::store()->get($this->getPrefixIdStore() . '-' . $key);
    }

    public function removeStore($key)
    {
        static::store()->delete($this->getPrefixIdStore() . '-' . $key);
    }
}