<?php

namespace App\Controller;

use App\DTO\WorkTimeDTO;
use App\Entity\WorkTime;
use App\Repository\EmployeeRepository;
use App\Repository\WorkTimeRepository;
use App\Utils\WorkTimeCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/api')]
class WorkTimeController extends AbstractController
{
    #[Route('/worktimes', methods: ['POST'])]
    public function registerWorkTime(
        #[MapRequestPayload] WorkTimeDTO $dto,
        EntityManagerInterface           $em,
        EmployeeRepository               $employeeRepo,
        WorkTimeRepository               $workTimeRepo
    ): JsonResponse
    {
        try {
            $employee = $employeeRepo->findOneBy(['uuid' => Uuid::fromString($dto->employeeUuid)]);
            if (!$employee) {
                throw new \InvalidArgumentException('Missing Employee');
            }

            $dto->validate();

            $start = new \DateTime($dto->start);
            $stop = new \DateTime($dto->stop);

            $workStart = clone $start;
            $workStart->setTime(0, 0);

            if ($workTimeRepo->findOneBy(['employee' => $employee, 'workTimeStart' => $workStart])) {
                throw new \Exception('Workday started already');
            }

            $workTime = new WorkTime();
            $workTime->setEmployee($employee)
                ->setStart($start)
                ->setStop($stop)
                ->setWorkTimeStart($workStart);

            $em->persist($workTime);
            $em->flush();

            return $this->json(['message' => 'Czas pracy został dodany!']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/worktimes/{uuid}/{date}', methods: ['GET'])]
    public function summary(
        string              $uuid,
        string              $date,
        EmployeeRepository  $employeeRepo,
        WorkTimeCalculator  $calculator,
        SerializerInterface $serializer
    ): JsonResponse
    {
        try {
            $employee = $employeeRepo->findOneBy(['uuid' => Uuid::fromString($uuid)]);
            if (!$employee) {
                throw new \Exception('Employee not found');
            }

            $workTimes = $calculator->getWorkTimesForSummary($employee, $date);
            $result = $calculator->calculateSummary($workTimes, $date);

            $serializedData = $serializer->serialize($result, 'json');
            return new JsonResponse($serializedData, 200, [], true);

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
