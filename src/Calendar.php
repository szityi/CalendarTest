<?php
/**
 * Created by PhpStorm.
 * User: Szityi
 * Date: 1/22/2018
 * Time: 18:08
 */

namespace Calendar;

use DateTimeInterface;

class Calendar implements CalendarInterface
{
    private $datetime;

    /**
     * @param DateTimeInterface $datetime
     */
    public function __construct(DateTimeInterface $datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * Get the day
     *
     * @return int
     */
    public function getDay()
    {
        return (int)date('j', $this->datetime->getTimestamp());
    }

    /**
     * Get the weekday (1-7, 1 = Monday)
     *
     * @return int
     */
    public function getWeekDay()
    {
        return (int)date('N', $this->datetime->getTimestamp());
    }

    /**
     * Get the first weekday of this month (1-7, 1 = Monday)
     *
     * @return int
     */
    public function getFirstWeekDay()
    {
        return (int)date('N', strtotime($this->datetime->format('Y-m')));
    }

    /**
     * Get the first week of this month (18th March => 9 because March starts on week 9)
     *
     * @return int
     */
    public function getFirstWeek()
    {
        return (int)date('W', strtotime($this->datetime->format('Y-m')));
    }

    /**
     * Get the number of days in this month
     *
     * @return int
     */
    public function getNumberOfDaysInThisMonth()
    {
        return (int)date('t', $this->datetime->getTimestamp());
    }

    /**
     * Get the number of days in the previous month
     *
     * @return int
     */
    public function getNumberOfDaysInPreviousMonth()
    {
        $datetime = clone $this->datetime;
        $datetime->sub(new \DateInterval('P1M'));
        !($this->datetime->format('d') == 31) ?: $datetime->sub(new \DateInterval('P1D'));
        return (int)date('t', $datetime->getTimestamp());
    }

    /**
     * Get the calendar array
     *
     * @return array
     */
    public function getCalendar()
    {
        $begin = new \DateTime($this->datetime->format('Y-m'));
        $begin->sub(new \DateInterval('P' . ($this->getFirstWeekDay() - 1) . 'D'));

        $end = new \DateTime($this->datetime->format('Y-m-t'));
        $end->add(new \DateInterval('P' . (8 - date('N', $end->getTimestamp())) . 'D'));

        $period = new \DatePeriod($begin, new \DateInterval('P1D'), $end);

        $endOfPrevWeek = clone $this->datetime;
        $endOfPrevWeek->sub(new \DateInterval('P1W'));

        $startOfPrevWeek = clone $endOfPrevWeek;
        $startOfPrevWeek->sub(new \DateInterval('P' . date('N', $endOfPrevWeek->getTimestamp()) . 'D'));

        $endOfPrevWeek->add(new \DateInterval('P' . (7 - date('N', $endOfPrevWeek->getTimestamp())) . 'D'));
        $endOfFirstWeek = $begin->add(new \DateInterval('P6D'));

        $calendar = [];
        foreach ($period as $day) {
            $highlight = false;
            if ($day > $startOfPrevWeek && $day <= $endOfPrevWeek && $this->datetime > $endOfFirstWeek) {
                $highlight = true;
            }
            $calendar[(int)date('W', $day->getTimestamp())][(int)$day->format('d')] = $highlight;
        }

        return $calendar;
    }
}