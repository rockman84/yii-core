<?php
namespace sky\yii\swiftmailer;

use Yii;
use yii\queue\Queue;


class Mailer extends \yii\swiftmailer\Mailer
{
    public $defaultFrom = null;

    public $queueName = 'queue';

    public function compose($view = null, array $params = [])
    {
        $parent = parent::compose($view, $params);
        $this->defaultFrom && $parent->setFrom($this->defaultFrom);
        return $parent;
    }

    public function pushQueue($params)
    {
        /* @var $queue Queue */
        $queue = Yii::$app->get($this->queueName);
        
        $queue->push(new MailerJob($params));
    }
}