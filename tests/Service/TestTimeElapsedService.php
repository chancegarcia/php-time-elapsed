<?php
/**
 * @package
 * @subpackage
 * @author      Chance Garcia <chance@garcia.codes>
 * @copyright   (C)Copyright 2013-2017 Chance Garcia, chancegarcia.com
 *
 *    The MIT License (MIT)
 *
 * Copyright (c) 2013-2017 Chance Garcia
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace Chance\PhpTimeElapsed\Test;

use PHPUnit\Framework\TestCase;

class TestTimeElapsedService extends TestCase
{
    /**
     * Throw an exception for no \DateInterval set
     *
     * @expectedException \LogicException
     */
    public function testServiceThrowsExceptionIfDateIntervalNotSet()
    {
        $service = new TimeElapsedService();
        $service->hasDaysElapsed(1);
        $this->fail('Expected exception was not triggered. please validate logic');
    }

    /**
     * Throww an exception for inverted \Dateinterval
     *
     * @expectedException \LogicException
     */
    public function testServiceThrowsExceptionForInvertedIntervalInConstructor()
    {
        $start16 = new \DateTime("2016-01-01");
        $end16 = new \DateTime("2016-12-31");

        $diff = $end16->diff($start16);
        $service = new TimeElapsedService($diff);
    }

    /**
     * Throww an exception for inverted \Dateinterval
     *
     * @expectedException \LogicException
     */
    public function testServiceThrowsExceptionForInvertedInterval()
    {
        $start16 = new \DateTime("2016-01-01");
        $end16 = new \DateTime("2016-12-31");

        $diff = $end16->diff($start16);
        $service = new TimeElapsedService();
        $service->setInterval($diff);
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testServiceHasTimeElapsedThrowsExceptionForZero()
    {
        $start16 = new \DateTime("2016-01-01");
        $end16 = new \DateTime("2016-12-31");

        $diff = $start16->diff($end16);
        $service = new TimeElapsedService();
        $service->hasTimeElapsed(0, 'year');
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testServiceHasTimeElapsedThrowsExceptionForNegativeValue()
    {
        $start16 = new \DateTime("2016-01-01");
        $end16 = new \DateTime("2016-12-31");

        $diff = $start16->diff($end16);
        $service = new TimeElapsedService();
        $service->hasTimeElapsed(-1, 'year');
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testServiceHasTimeElapsedThrowsExceptionForUnknownTimeUnit()
    {
        $start16 = new \DateTime("2016-01-01");
        $end16 = new \DateTime("2016-12-31");

        $diff = $start16->diff($end16);
        $service = new TimeElapsedService();
        $service->hasTimeElapsed(1, 'monkeys');
    }

    /**
     * test time elapsed with time unit value and unit string
     */
    public function testServiceHasTimeElapsed()
    {
        $start16 = new \DateTime("2016-01-01");
        $end16 = new \DateTime("2016-12-31");
        $start17 = clone $start16;
        $start17->modify("+1 year");

        $yearDiff = $start16->diff($start17);
        $notYearDiff = $start16->diff($end16);

        $service = new TimeElapsedService();

        $service->setInterval($yearDiff);
        $this->assertTrue($service->hasTimeElapsed(1, 'year'));

        $service->setInterval($notYearDiff);
        $this->assertFalse($service->hasTimeElapsed(1, 'year'));
    }

    /**
     * test years elapsed
     *
     * @depends testServiceHasTimeElapsed
     */
    public function testServiceHasYearsElapsed()
    {
        $start16 = new \DateTime("2016-01-01");
        $end16 = new \DateTime("2016-12-31");
        $start17 = clone $start16;
        $start17->modify("+1 year");

        $yearDiff = $start16->diff($start17);
        $notYearDiff = $start16->diff($end16);

        $service = new TimeElapsedService();

        $service->setInterval($yearDiff);
        $this->assertTrue($service->hasYearsElapsed(1));

        $service->setInterval($notYearDiff);
        $this->assertFalse($service->hasYearsElapsed(1));

    }

    public function testServiceConvertYearsToMonths()
    {
        $numYears = 2;
        $this->assertEquals(
            TimeElapsedService::MONTHS_PER_YEAR * $numYears,
            TimeElapsedService::convertYearsToMonths($numYears)
        );
    }

    /**
     *
     * @depends testServiceHasTimeElapsed
     */
    public function testServiceHasMonthsElapsed()
    {
        $start16 = new \DateTime("2016-01-01");
        $end16 = new \DateTime("2016-12-31");
        $start17 = clone $start16;
        $start17->modify("+1 year");
        $end17 = clone $end16;
        $end17->modify("+1 year");

        $service = new TimeElapsedService();
        // less than 12
        $service->setInterval($start16->diff($end16));
        $this->assertTrue($service->hasMonthsElapsed(11));
        $this->assertFalse($service->hasMonthsElapsed(12));

        // 12 months
        $service->setInterval($start16->diff($start17));
        $this->assertTrue($service->hasMonthsElapsed(12));

        // more than 12
        $service->setInterval($start16->diff($end17));
        $this->assertTrue($service->hasMonthsElapsed(13));

    }

    public function testServiceConvertDaysToWeeks()
    {
        // less than 7 days
        $lt7 = 5;
        $this->assertEquals(
            (int) floor($lt7/TimeElapsedService::DAYS_PER_WEEK),
            TimeElapsedService::convertDaysToWeeks($lt7)
        );

        // 7 days
        $n = 7;
        $this->assertEquals(
            (int) floor($n/TimeElapsedService::DAYS_PER_WEEK),
            TimeElapsedService::convertDaysToWeeks($n)
        );

        // more than 7
        $gt7 = 8;
        $this->assertEquals(
            (int) floor($gt7/TimeElapsedService::DAYS_PER_WEEK),
            TimeElapsedService::convertDaysToWeeks($gt7)
        );

    }

    /**
     * @depends testServiceConvertDaysToWeeks
     */
    public function testServiceHasWeeksElapsed()
    {
        $start16 = new \DateTime("2016-01-01");
        $end = new \DateTime("2016-02-01");

        $diff = $start16->diff($end);
        $service = new TimeElapsedService($diff);
        $this->assertTrue($service->hasWeeksElapsed(4));
        $this->assertFalse($service->hasWeeksElapsed(8));
    }

    public function testServiceHasDaysElapsed()
    {
        $start16 = new \DateTime("2016-01-01");
        $end = new \DateTime("2016-02-01");

        $diff = $start16->diff($end);

        $service = new TimeElapsedService($diff);
        $this->assertTrue($service->hasDaysElapsed(4));
        $this->assertFalse($service->hasDaysElapsed(38));
    }

    public function testServiceConvertDaysToHours()
    {
        $numDays = 2;
        $this->assertEquals(
            TimeElapsedService::HOURS_PER_DAY * $numDays,
            TimeElapsedService::convertDaysToHours($numDays)
        );
    }

    /**
     * @depends testServiceConvertDaysToHours
     */
    public function testServiceHasHoursElapsed()
    {
        $start = new \DateTime("2017-08-22 00:00:00");
        $end = clone  $start;
        $end->modify("+2 hours");

        $diff = $start->diff($end);
        $service = new TimeElapsedService($diff);

        // less than 24
        $this->assertTrue($service->hasHoursElapsed(2));
        $this->assertFalse($service->hasHoursElapsed(23));

        // 24 hours
        $end->modify("+22 hours");
        $diff = $start->diff($end);
        $service->setInterval($diff);
        $this->assertTrue($service->hasHoursElapsed(24));

        // greater than 24
        $end->modify("+4 days");
        $diff = $start->diff($end);
        $service->setInterval($diff);
        $this->assertTrue($service->hasHoursElapsed(36));

    }

    public function testServiceConvertHoursToMinutes()
    {
        $n = 2;
        $this->assertEquals(
            TimeElapsedService::MINUTES_PER_HOUR * $n,
            TimeElapsedService::convertHoursToMinutes($n)
        );
    }

    public function testServiceHasMinutesElapsed()
    {
        $start = new \DateTime("2017-08-22 00:00:00");
        $end = clone  $start;
        $end->modify("+10 minutes");

        $diff = $start->diff($end);
        $service = new TimeElapsedService($diff);

        // less than 60
        $this->assertTrue($service->hasMinutesElapsed(5));
        $this->assertFalse($service->hasMinutesElapsed(20));

        // 60 minutes
        $end->modify("+50 minutes");
        $diff = $start->diff($end);
        $service->setInterval($diff);
        $this->assertTrue($service->hasMinutesElapsed(60));

        // greater than 60
        $end->modify("+12 hours");
        $diff = $start->diff($end);
        $service->setInterval($diff);
        $this->assertTrue($service->hasMinutesElapsed(90));
    }

    public function testServiceConvertMinutesToSeconds()
    {
        $n = 2;
        $this->assertEquals(
            TimeElapsedService::SECONDS_PER_MINUTE * $n,
            TimeElapsedService::convertHoursToMinutes($n)
        );
    }

    /**
     * @depends testServiceConvertMinutesToSeconds
     */
    public function testServiceHasSecondsElapsed()
    {
        $start = new \DateTime("2017-08-22 00:00:00");
        $end = clone  $start;
        $end->modify("+10 seconds");

        $diff = $start->diff($end);
        $service = new TimeElapsedService($diff);

        // less than 60
        $this->assertTrue($service->hasSecondsElapsed(5));
        $this->assertFalse($service->hasSecondsElapsed(20));

        // 60 seconds
        $end->modify("+50 seconds");
        $diff = $start->diff($end);
        $service->setInterval($diff);
        $this->assertTrue($service->hasSecondsElapsed(60));

        // greater than 60
        $end->modify("+12 minutes");
        $diff = $start->diff($end);
        $service->setInterval($diff);
        $this->assertTrue($service->hasSecondsElapsed(90));
    }
}