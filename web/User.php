<?php
namespace sky\yii\web;

use sky\yii\helpers\ArrayHelper;
use Yii;
use yii\web\Cookie;

/**
 * Class User
 * @package sky\yii\web
 *
 * @property-read int $guestId;
 */
class User extends \yii\web\User
{
    /**
     * cookie name for guest ID
     * @var string
     */
    public $guestIdParam = '__skyGuestId__';

    /**
     * enable guest ID if false, $guestId will always return nul
     * @var bool
     */
    public $enableGuestId = false;

    /**
     * days of cookie guest ID default is 90 days
     * @var int
     */
    public $guestIdExpire = 86400 * 90;

    /**
     * default cookie params
     * @var array
     */
    public $defaultCookieParams = [];

    protected $_guestId = null;

    /**
     * Get Guest Visitor ID
     * @return mixed|string
     * @throws \yii\base\Exception
     */
    public function getGuestId()
    {
        if (!$this->enableGuestId) {
            $this->destroyGuestId();
            return null;
        }
        $this->_guestId  = Yii::$app->request->getCookies()->getValue($this->guestIdParam);
        if (!$this->_guestId) {
            $cookie = $this->createCookie([
                'name' => $this->guestIdParam,
                'value' => Yii::$app->security->generateRandomString(32) . '-' . time(),
                'expire' => time() + $this->guestIdExpire,
            ]);
            $this->_guestId = $cookie->value;
            Yii::$app->response->cookies->add($cookie);
        }
        return $this->_guestId;
    }

    /**
     * destroy guest ID cookie
     */
    public function destroyGuestId()
    {
        $this->_guestId = null;
        Yii::$app->response->cookies->remove($this->guestIdParam);
    }

    /**
     * create cookies
     * @param array $params
     * @return Cookie
     */
    public function createCookie($params = [])
    {
        return new Cookie(ArrayHelper::merge($this->defaultCookieParams, $params));
    }
}