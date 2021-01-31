<?php

namespace App\Controller\Traits;

trait ResolveReportsTrait
{
    /**
     * @var array Open hours of work as an array
     */
    private $workSchedule = [

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
        foreach ($reports as $report) {
            $workingTime = $this->getWorkingTime($report);
            //var_dump($workingTime);
        }

        return [];
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

        // Get a large interval
        $midnightStartDate = (new \DateTime($reportStartDate))->setTime(0, 0);
        $midnightEndDate = (new \DateTime($reportEndDate))->modify('+1 day')->setTime(0, 0);

        // Initialize a period with this interval
        $largePeriod = new \DatePeriod($midnightStartDate, new \DateInterval('P1D'), $midnightEndDate);

        // Number of worked day
        $nbrWorkedDay = 0;
        foreach ($largePeriod as $dayDate) {
            if ($dayDate->format('N') < 6) {
                ++$nbrWorkedDay;
            }
        }
        // Number of worked hours
        $nbrWorkedHours = $nbrWorkedDay * 7;

        if (!$this->isWeekend($startDate)) {
            // enlever la diff avec le début
        }

        if (!$this->isWeekend($endDate)) {
            // enlever la différence avec la fin
        }

        return 0;
    }

    /**
     * @param \DateTime $date
     *
     * @return bool
     */
    private function isWeekend(\DateTime $date): bool
    {
        $weekDay = $date->format('N');

        return $weekDay >= 6;
    }
}
