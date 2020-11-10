<?php

namespace App\Services;

class Helper
{
    public static function getDT($date,$is_timestamp = false)
    {
        if (is_int($date)) {
            return $is_timestamp ? (int)$date : date('Y-m-d H:i:s',$date);
        }
        if (strlen($date) < 12) {
            return $is_timestamp ? strtotime($date.' 00:00:00') : $date.' 00:00:00';
        }
        return $is_timestamp ? strtotime($date) : $date;
    }

    public static function onlyDT($date)
    {
        if (strlen($date) < 12) {
            return $date;
        }
        $arr = explode(' ',$date);
        return $arr[0];
    }

    public static function getDaysArr($start,$end)
    {
        $start = self::onlyDT($start).' 00:00:00';
        $start_dt = strtotime($start);
        $end = self::onlyDT($end).' 23:59:59';
        $end_dt = strtotime($end);
        $res = [];
        $curr = $start_dt;
        do {
            $res[] = date('Y-m-d H:i:s',$curr);
            $curr += 24*3600;
        } while ($curr < $end_dt);
        return $res;
    }
}
