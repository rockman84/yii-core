<?php
namespace sky\yii\mailgun;

use yii\mail\BaseMailer;

class Mailer extends BaseMailer
{
    public $messageClass = 'sky\yii\mailgun\Message';

    public $domain;

    public $key;

    public $tags = [];

    public $clicksTrackingMode;

    protected $_mailgun;

    protected function sendMessage($message)
    {
        $mailer = $this->getMailgunMailer();


        $message->setClickTracking($this->clicksTrackingMode)
            ->addTags($this->tags);

        Yii::info('Sending email', __METHOD__);
        $response = $this->getMailgunMailer()->post(
            "{$this->domain}/messages",
            $message->getMessage(),
            $message->getFiles()
        );

        Yii::info('Response : '.print_r($response, true), __METHOD__);

        return true;
    }

    public function getMailgun()
    {
        if (!$this->_mailgun) {
            $this->_mailgun = new Mailgun($this->key);
        }
        return $this->_mailgun;
    }
}