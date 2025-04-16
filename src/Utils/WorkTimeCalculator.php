<?php
declare(strict_types=1);

namespace App\Utils;

use App\Entity\Employee;
use App\Repository\WorkTimeRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WorkTimeCalculator
{
    public function __construct(
        private readonly ParameterBagInterface $params,
        private readonly WorkTimeRepository    $workTimeRepo
    )
    {
    }

    public function getWorkTimesForSummary(Employee $employee, string $dateString): array
    {
        if (strlen($dateString) === 7) {
            $date = \DateTime::createFromFormat('Y-m', $dateString);
            $date->modify('first day of this month');
            return $this->workTimeRepo->findByEmployeeAndMonth($employee, $date);
        }

        $date = \DateTime::createFromFormat('Y-m-d', $dateString);
        return $this->workTimeRepo->findByEmployeeAndDate($employee, $date);
    }

    public function calculateSummary(array $workTimes, string $dateString): array
    {
        $standard = 0;
        $overtime = 0;
        $total = 0;
        $totalValue = 0;

        if (strlen($dateString) === 10) {
            foreach ($workTimes as $workTime) {
                $interval = $workTime->getStart()->diff($workTime->getStop());
                $minutes = ($interval->h * 60) + $interval->i;
                $hours = (round($minutes / 30) * 30) / 60; // round do 30 min
                $standard += min($hours, $this->params->get('API_DAILY_STANDARD_HOURS'));
                $overtime += max($hours - $this->params->get('API_DAILY_STANDARD_HOURS'), 0);
            }

            $total = round($standard + $overtime, 1);

            $totalValue = round(
                ($standard * $this->params->get('API_STANDARD_RATE')) + ($overtime * ($this->params->get('API_STANDARD_RATE') * 2)),
                2
            );
        }

        if (strlen($dateString) === 7) {
            foreach ($workTimes as $workTime) {
                $interval = $workTime->getStart()->diff($workTime->getStop());
                $minutes = $interval->h * 60 + $interval->i;
                $hours = round($minutes / 30) * 30 / 60;
                $total += $hours;
            }

            $standard = min($total, $this->params->get('API_MONTHLY_STANDARD_HOURS'));
            $overtime = max($total - $this->params->get('API_MONTHLY_STANDARD_HOURS'), 0);

            $totalValue = ($standard * $this->params->get('API_STANDARD_RATE')) + ($overtime * ($this->params->get('API_STANDARD_RATE') * 2));

        }

        return [
            'totalHours' => $total,
            'standardHours' => round($standard, 1),
            'overtimeHours' => round($overtime, 1),
            'totalValue' => $totalValue
        ];
    }

}
