<?php
namespace sky\yii\client\google;

use yii\base\Component;
use Yii;

/**
 * Class Google
 * @package common\components\google
 *
 * @property GoogleCalendar $calendar
 */
class Google extends Component
{

    private $_calendar;

    public $authConfig = '@app/config/googleServiceAccount.json';

    public $appName = 'Google Calendar API Yii2';

    /**
     * @return \Google_Client
     * @throws \Google\Exception
     */
    public function createClient($scope)
    {
        $client = new \Google_Client();
        $client->setApplicationName($this->appName);
        $client->setAuthConfig(Yii::getAlias($this->authConfig));
        $client->setScopes($scope);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        return $client;
    }

    /**
     * @return GoogleCalendar
     * @throws \Google\Exception
     */
    public function getCalendar()
    {
        if (!$this->_calendar) {
            $client = $this->createClient(GoogleCalendar::CALENDAR);
            $this->_calendar = new GoogleCalendar($client);
        }
        return $this->_calendar;
    }

}