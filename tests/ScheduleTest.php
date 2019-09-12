<?php

namespace App\Tests;

use App\Entity\Schedule;
use PHPUnit\Framework\TestCase;

class ScheduleTest extends TestCase
{

    /*
 * L-D: 24H
L-D: 06:00-22:00
L-D: 07:00-23:00
L-D: 07:00-22:00
L-V: 07:00-21:45; S-D: 08:00-21:45
L-D: 07:00-21:00
L-S: 07:30-21:30
L-S: 10:00-22:00
L-D: 06:30-22:30
L-S: 06:00-23:00; D: 07:30-23:00
L-V: 07:00-21:30
L-D: 06:00-23:00
L-D: 05:00-23:00
L-V: 06:00-23:00; S: 06:00-22:00; D: 06:00-23:00
L-V: 06:00-22:00; S: 07:00-22:00; D: 08:00-22:00
L-S: 08:00-22:00; D: 09:00-21:00
L-D: 06:00-21:30
L-J: 06:00-23:00; V-D: 06:00-00:00
L-D: 06:00-00:00
L: 06:00-00:00
L-S: 07:00-23:00; D: 08:00-23:00
L-V: 06:00-22:00; S: 07:30-15:30; D: 09:00-15:00
L-S: 07:00-21:30; D: 09:00-15:00
L-S: 07:00-22:00
L-S: 07:00-22:00; D: 08:00-21:00
 */


    public function testSchedule()
    {
        $schedule = new Schedule('L-V: 06:00-23:00; S: 06:00-22:00; D: 06:00-23:00');
        $this->assertTrue($schedule->isOpen('M', '19:00'));
        $this->assertTrue($schedule->isOpen('X', '06:00'));
        $this->assertTrue($schedule->isOpen('D', '23:00'));
        $this->assertTrue($schedule->isClose('V', '23:30'));
        $this->assertTrue($schedule->isClose('S', '04:30'));
        $this->assertTrue($schedule->isClose('L', '00:00'));

        $schedule = new Schedule('L: 06:00-00:00');
        $this->assertTrue($schedule->isOpen('L', '19:00'));
        $this->assertTrue($schedule->isOpen('L', '23:00'));
        $this->assertTrue($schedule->isClose('S', '04:30'));
        $this->assertTrue($schedule->isClose('L', '00:00'));
        $this->assertTrue($schedule->isClose('L', '05:00'));

        $schedule = new Schedule('L-D: 24H');
        $this->assertTrue($schedule->isOpen('M', '19:00'));
        $this->assertTrue($schedule->isOpen('X', '06:00'));
        $this->assertTrue($schedule->isOpen('D', '23:00'));
        $this->assertTrue($schedule->isOpen('V', '23:30'));
        $this->assertTrue($schedule->isOpen('S', '04:30'));
        $this->assertTrue($schedule->isOpen('L', '00:00'));

    }
}
