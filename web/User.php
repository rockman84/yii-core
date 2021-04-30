<?php
namespace sky\yii\web;

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

    protected $_guestId = null;

    /**
     * Get Guest Visitor ID
     * @return mixed|string
     * @throws \yii\base\Exception
     */
    public function getGuestId()
    {
        $this->_guestId  = Yii::$app->request->getCookies()->getValue($this->guestIdParam);
        if (!$this->_guestId) {
            $cookie = new Cookie([
                'name' => $this->guestIdParam,
                'value' => Yii::$app->security->generateRandomString(64),
                'expire' => time() + (86400 * 365),
            ]);
            $this->_guestId = $cookie->value;
            Yii::$app->response->cookies->add($cookie);
        }
        return $this->_guestId;
    }
}