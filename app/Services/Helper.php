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

    public static function getDaysArr($start,$end)
    {
        $start = self::getDT($start);
        $start_dt = strtotime($start);
        $end = self::getDT($end);
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
