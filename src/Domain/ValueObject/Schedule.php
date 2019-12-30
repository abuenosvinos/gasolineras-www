<?php


namespace App\Domain\ValueObject;


class Schedule
{
    const DAYS = ['L','M','X','J','V','S','D'];
    private $schedule;

    public function __construct($schedule)
    {
        $this->schedule = $schedule;
    }

    public function isOpen($day = null, $hour = null)
    {
        $day = (isset($day)) ? $day : self::DAYS[date('N') - 1];
        $hour = (isset($hour)) ? $hour : date('H:00');

        $schedule = explode(';', $this->schedule);
        foreach ($schedule as $item) {
            $item = explode(' ', trim($item));

            $day_schedule = explode('-', str_replace(':', '', $item[0]));
            $day_ini = $day_schedule[0];
            $day_fin = isset($day_schedule[1]) ? $day_schedule[1] : $day_schedule[0];
            $day_list = [];
            foreach (self::DAYS as $tmp) {
                if ($tmp == $day_fin) {
                    $day_list[] = $tmp;
                    break;
                }
                if ($tmp == $day_ini) {
                    $day_list[] = $tmp;
                } else if (count($day_list) > 0) {
                    $day_list[] = $tmp;
                }
            }

            $hour_schedule = $item[1];
            if ($hour_schedule == '24H') {
                $hour_ini = 0;
                $hour_fin = 0;
                $hour_time = 12;
            } else {
                $hour_schedule = explode('-', $hour_schedule);
                $hour_ini = intval(str_replace(':', '', $hour_schedule[0]));
                $hour_fin = intval(str_replace(':', '', $hour_schedule[1]));
                $hour_time = intval(str_replace(':', '', $hour));
            }

            if (in_array($day,$day_list) && (($hour == '24H') || ($hour_ini <= $hour_time && ($hour_time <= $hour_fin || $hour_fin == 0)))) {
                return true;
            }
        }

        return false;
    }

    public function isClose($day = null, $hour = null)
    {
        return !$this->isOpen($day, $hour);
    }
}