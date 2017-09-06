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

namespace Chance\PhpTimeElapsed\Service;


class TimeElapsedService implements TimeElapsedServiceInterface
{
    /**
     * @var \DateInterval
     */
    protected $interval;

    /**
     * TimeElapsedService constructor.
     * @param \DateInterval|null $interval
     */
    public function __construct(\DateInterval $interval = null)
    {
        if ($interval) {
            $this->setInterval($interval);
        }
    }

    /**
     * @return \DateInterval
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @param \DateInterval $interval
     */
    public function setInterval(\DateInterval $interval)
    {
        if ($interval->invert) {
            throw new \LogicException("date interval must not be inverted", 10000);
        }

        $this->interval = $interval;
    }

    /**
     * @param $amount
     * @param $unit
     * @return bool
     * @throws \UnexpectedValueException
     * @throws \LogicException
     */
    public function hasTimeElapsed($amount, $unit = 'days')
    {
        if (!$this->getInterval() instanceof \DateInterval) {
            throw new \LogicException("date interval has not been set.", 20000);
        }

        if (!is_numeric($amount)) {
            throw new \LogicException("please give a numeric value to evaluate. given (" . $amount . ")", 30000);
        }

        if ($amount < 1) {
            throw new \UnexpectedValueException("please indicate a positive value to evaluate. given (" . $unit .")", 40000);
        }

        if (!in_array($unit, self::VALID_TIME_UNITS)) {
            throw new \UnexpectedValueException("please indicate a valid unit of time. given (" . $unit . ")", 50000);
        }

        $totalDays = $this->getInterval()->days;

        switch (strtolower($unit)) {
            case 'year':
                // no break
            case 'years':
                $actual = $this->getInterval()->y;
                break;
            case 'month':
                // no break
            case 'months':
                $actual = $this->getActualMonths();
                break;
            case 'week':
                // no break
            case 'weeks':
                $actual = self::convertDaysToWeeks($totalDays);
                break;
            case 'hour':
                // no break
            case 'hours':
                $actual = $this->getActualHours();
                break;
            case 'minute':
                // no break
            case 'minutes':
                $actual = $this->getActualMinutes();
                break;
            case 'second':
                // no break
            case 'seconds':
                $actual = $this->getActualSeconds();
                break;
            case 'day':
                // no break
            case'days':
                // no break;
            default:
                $actual = $totalDays;
                break;
        }

        return $actual >= $amount;
    }

    final public static function convertYearsToMonths($years)
    {
        return $years * self::MONTHS_PER_YEAR;
    }

    final public static function convertDaysToWeeks($days)
    {
        return floor($days/self::DAYS_PER_WEEK);
    }

    final public static function convertDaysToHours($days)
    {
        return $days * self::HOURS_PER_DAY;
    }

    final public static function convertHoursToMinutes($hours)
    {
        return $hours * self::MINUTES_PER_HOUR;
    }

    final public static function convertMinutesToSeconds($minutes)
    {
        return $minutes * self::SECONDS_PER_MINUTE;
    }

    public function hasYearsElapsed($amount)
    {
        return $this->hasTimeElapsed($amount, 'years');
    }

    public function hasMonthsElapsed($amount)
    {
        return $this->hasTimeElapsed($amount, 'months');
    }

    public function hasWeeksElapsed($amount)
    {
        return $this->hasTimeElapsed($amount, 'weeks');
    }

    public function hasDaysElapsed($amount)
    {
        return $this->hasTimeElapsed($amount, 'days');
    }

    public function hasHoursElapsed($amount)
    {
        return $this->hasTimeElapsed($amount, 'hours');
    }

    public function hasMinutesElapsed($amount)
    {
        return $this->hasTimeElapsed($amount, 'minutes');
    }

    public function hasSecondsElapsed($amount)
    {
        return $this->hasTimeElapsed($amount, 'seconds');
    }

    /**
     * @return int
     */
    public function getActualMonths()
    {
        $actual = $this->getInterval()->m;
        $years = $this->getInterval()->y;
        if ($years) {
            $actual += self::convertYearsToMonths($years);
        }

        return $actual;
    }

    /**
     * @return int
     */
    public function getActualHours()
    {
        // we use days because it's available and accounts for leap years
        $totalDays = $this->getInterval()->days;
        $actual = $this->getInterval()->h;
        $actual += self::convertDaysToHours($totalDays);

        return $actual;
    }

    /**
     * @return int
     */
    public function getActualMinutes()
    {
        $actual = $this->getInterval()->i;
        $hours = $this->getActualHours();
        $actual += self::convertHoursToMinutes($hours);

        return $actual;
    }

    /**
     * @return int
     */
    public function getActualSeconds()
    {
        $actual = $this->getInterval()->s;
        $minutes = $this->getActualMinutes();
        $actual += self::convertMinutesToSeconds($minutes);

        return $actual;
    }
}