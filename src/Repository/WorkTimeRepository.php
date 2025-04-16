<?php

namespace App\Repository;

use App\Entity\Employee;
use App\Entity\WorkTime;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class WorkTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkTime::class);
    }

    public function findByEmployeeAndDate(Employee $employee, DateTime $date): array
    {
        return $this->createQueryBuilder('w')
            ->where('w.employee = :employee')
            ->andWhere('w.workTimeStart = :date')
            ->setParameter('employee', $employee)
            ->setParameter('date', $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function findByEmployeeAndMonth(Employee $employee, DateTime $month): array
    {
        $start = clone $month;
        $end = clone $month;
        $end->modify('last day of this month');

        return $this->createQueryBuilder('w')
            ->where('w.employee = :employee')
            ->andWhere('w.workTimeStart BETWEEN :start AND :end')
            ->setParameter('employee', $employee)
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }
}
