<?php
namespace sky\yii\security;

use Yii;

class Security extends \yii\base\Security
{
    public function uniqueID($prefix = '', $more_entropy = false)
    {
        return uniqid($prefix, $more_entropy);
    }

    public function generateStringNumber($length = 16, $uppercase = true, $number = true)
    {
        $chars =  'abcdefghijklmnopqrstuvwxyz';
        if ($uppercase) {
            $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        if ($number) {
            $chars .= '0123456789';
        }
        $str = '';
        $max = strlen($chars) - 1;
        for ($i=0; $i < $length; $i++)
            $str .= $chars[random_int(0, $max)];

        return $str;
    }

    public function generateSerial($partLength = 8, $part = 4, $glue = '-', $uppercase = false)
    {
        $string = [];
        for ($i = 0; $i < $part; $i++) {
            $string[] = $this->generateStringNumber($partLength, $uppercase);
        }
        return implode($glue, $string);
    }
}