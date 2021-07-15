<?php
namespace sky\yii\base;

use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;

class JobRetryable extends BaseObject implements RetryableJobInterface
{
    public function getTtr()
    {
        return 15 * 60;
    }

    public function canRetry($attempt, $error)
    {
        return $attempt < 10 && $error;
    }

    public function execute($queue)
    {
        return null;
    }
}