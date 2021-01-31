<?php

namespace App\Controller\Traits;

/**
 * Trait ResolveReportsTrait.
 */
trait ResolveReportsTrait
{
    /**
     * @var array
     *
     * - Du lundi au jeudi inclus : 9h-12h 14h-18h
     * - Le vendredi : 9h-12h 13h-17h
     */
    private array $workSchedule = [
        'normal' => [
            'morning' => [
                'start' => 9 * 3600,
                'end' => 12 * 3600,
            ],
            'afternoon' => [
                'start' => 14 * 3600,
                'end' => 18 * 3600,
            ],
        ],
        'special' => [
            'morning' => [
                'start' => 9 * 3600,
                'end' => 12 * 3600,
            ],
            'afternoon' => [
                'start' => 13 * 3600,
                'end' => 17 * 3600,
            ],
        ],
    ];

    /**
     * @param array $reports
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function resolveReports(array $reports): array
    {
        $resolvedReports = [];

        foreach ($reports as $report) {
            $resolvedReport = [];
            $workingTime = $this->getWorkingTime($report);
            $resolvedReport['workedTime'] = sprintf('%02d:%02d', (int) $workingTime, fmod($workingTime, 1) * 60);

            \array_push($resolvedReports, \array_merge($report, $resolvedReport));
        }

        return $resolvedReports;
    }

    /**
     * @param array $report
     *
     * @return float
     *
     * @throws \Exception
     */
    private function getWorkingTime(array $report): float
    {
        $reportStartDate = $report['start_date'];
        $reportEndDate = $report['end_date'];
        $startDate = new \DateTime($reportStartDate);
        $endDate = new \DateTime($reportEndDate);

        // Worked seconds in large interval (without considering start time and end time)
        $nbrWorkedSeconds = $this->getWorkingTimeMidnightMidnight($reportStartDate, $reportEndDate);

        // Now we need to remove the intervals with start hour and end hour
        if (!$this->isWeekend($startDate)) {
            $secondsToRemove = $this->isFriday($startDate) ?
                $this->calculationIntervalStart('special', $this->getSecondsElapsedSinceMidnight($startDate))
                : $this->calculationIntervalStart('normal', $this->getSecondsElapsedSinceMidnight($startDate));
            $nbrWorkedSeconds -= $secondsToRemove;
        }

        if (!$this->isWeekend($endDate)) {
            $secondsToRemove = $this->isFriday($endDate) ?
                $this->calculationIntervalEnd('special', $this->getSecondsElapsedSinceMidnight($endDate))
                : $this->calculationIntervalEnd('normal', $this->getSecondsElapsedSinceMidnight($endDate));
            $nbrWorkedSeconds -= $secondsToRemove;
        }

        return $nbrWorkedSeconds / 3600;
    }

    /**
     * @param string $keyName
     * @param int    $currentSeconds
     *
     * @return int
     */
    private function calculationIntervalStart(string $keyName, int $currentSeconds): int
    {
        $schedule = $this->workSchedule[$keyName];
        $secondsToRemove = 0;

        if ($currentSeconds >= $schedule['morning']['start'] && $currentSeconds <= $schedule['morning']['end']) {
            $secondsToRemove = $currentSeconds - $schedule['morning']['start'];
        } elseif ($currentSeconds > $schedule['morning']['end'] && $currentSeconds < $schedule['afternoon']['start']) {
            $secondsToRemove = 3 * 3600;
        } elseif ($currentSeconds >= $schedule['afternoon']['start'] && $currentSeconds <= $schedule['afternoon']['end']) {
            $secondsToRemove = 3 * 3600 + ($currentSeconds - $schedule['afternoon']['start']);
        } elseif ($currentSeconds > $schedule['afternoon']['end']) {
            $secondsToRemove = 8 * 3600;
        }

        return $secondsToRemove;
    }

    /**
     * @param string $keyName
     * @param int    $currentSeconds
     *
     * @return int
     */
    private function calculationIntervalEnd(string $keyName, int $currentSeconds): int
    {
        $schedule = $this->workSchedule[$keyName];
        $secondsToRemove = 0;

        if ($currentSeconds < $schedule['morning']['start']) {
            $secondsToRemove = 8 * 3600;
        } elseif ($currentSeconds >= $schedule['morning']['start'] && $currentSeconds <= $schedule['morning']['end']) {
            $secondsToRemove = 4 * 3600 + ($schedule['morning']['end'] - $currentSeconds);
        } elseif ($currentSeconds > $schedule['morning']['end'] && $currentSeconds < $schedule['afternoon']['start']) {
            $secondsToRemove = 4 * 3600;
        } elseif ($currentSeconds >= $schedule['afternoon']['start'] && $currentSeconds <= $schedule['afternoon']['end']) {
            $secondsToRemove = $schedule['afternoon']['end'] - $currentSeconds;
        }

        return $secondsToRemove;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     *
     * @return int
     *
     * @throws \Exception
     */
    private function getWorkingTimeMidnightMidnight(string $startDate, string $endDate): int
    {
        // Get a large interval
        $midnightStartDate = (new \DateTime($startDate))->setTime(0, 0);
        $midnightEndDate = (new \DateTime($endDate))->modify('+1 day')->setTime(0, 0);

        // Initialize a period with this interval
        $largePeriod = new \DatePeriod($midnightStartDate, new \DateInterval('P1D'), $midnightEndDate);

        // Number of worked day
        $nbrWorkedDay = 0;
        foreach ($largePeriod as $dayDate) {
            if ($dayDate->format('N') < 6) {
                ++$nbrWorkedDay;
            }
        }

        // Number of worked seconds
        return $nbrWorkedDay * 7 * 3600;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return bool
     */
    private function isWeekend(\DateTime $dateTime): bool
    {
        return $dateTime->format('N') >= 6;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return bool
     */
    private function isFriday(\DateTime $dateTime): bool
    {
        return 5 === $dateTime->format('N');
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return int
     */
    private function getSecondsElapsedSinceMidnight(\DateTime $dateTime): int
    {
        $hoursInSeconds = (int) $dateTime->format('H') * 3600;
        $minutesInSeconds = (int) $dateTime->format('i') * 60;
        $seconds = (int) $dateTime->format('s');

        return $hoursInSeconds + $minutesInSeconds + $seconds;
    }
}
