<?php
namespace sky\yii\helpers;

use yii\db\ActiveQuery;

class DateRangeHelper
{
    /**
     * @param null $month
     * @param null $year
     * @return object
     */
    public static function getTimeMonthRage($month = null, $year = null)
    {
        if ($month == null) {
            $month = date('M');
        }
        if ($year == null) {
            $year = date('Y');
        }
        return (object) [
            'start' => strtotime("first day of {$month} {$year} 00:00:00"),
            'end' => strtotime("last day of {$month} {$year} 23:59:59"),
        ];
    }

    /**
     * @param null $day
     * @param null $month
     * @param null $year
     * @return object
     */
    public static function getTimeDayRange($day = null, $month = null, $year = null)
    {
        if ($day == null) {
            $day = date('d');
        }
        if ($month == null) {
            $month = date('M');
        }
        if ($year == null) {
            $year = date('Y');
        }
        return (object) [
            'start' => strtotime("{$day} {$month} {$year} 00:00:00"),
            'end' => strtotime("{$day} {$month} {$year} 23:59:59"),
        ];
    }

    /**
     * @param ActiveQuery $query
     * @param $attribute
     * @param null $month
     * @param null $year
     * @return ActiveQuery
     */
    public static function addQueryMonthRange(ActiveQuery $query, $attribute, $month = null, $year = null)
    {
        $ranges = static::getTimeMonthRage($month, $year);
        $query->andWhere(['>=', $attribute, $ranges->start]);
        $query->andWhere(['<=', $attribute, $ranges->end]);
        return $ranges;
    }

    /**
     * @param ActiveQuery $query
     * @param $attribute
     * @param null $day
     * @param null $month
     * @param null $year
     * @return ActiveQuery
     */
    public static function addQueryDayRange(ActiveQuery $query, $attribute, $day = null, $month = null, $year = null)
    {
        $ranges = static::getTimeDayRange($day, $month, $year);
        $query->andWhere(['>=', $attribute, $ranges->start]);
        $query->andWhere(['<=', $attribute, $ranges->end]);
        return $ranges;
    }
}