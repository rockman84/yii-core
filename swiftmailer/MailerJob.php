<?php
namespace sky\yii\swiftmailer;

use sky\yii\base\JobRetryable;
use Yii;

class MailerJob extends JobRetryable
{
    public $mailerName = 'mailer';

    public $template;

    public $to;

    public $subject;

    public function execute($queue)
    {
        $mailer = $this->getMailer();
        $mailer->compose($this->template)
            ->setSubject($this->subject)
            ->send();
    }

    /**
     * @return Mailer|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getMailer()
    {
        return Yii::$app->get($this->mailerName);
    }
}