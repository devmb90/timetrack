<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\WorkTimeDTO;
use App\Entity\WorkTime;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api')]
class WorkTimeController extends AbstractController
{
    #[Route('/worktimes', methods: ['POST'])]
    public function registerWorkTime(
        #[MapRequestPayload] WorkTimeDTO $dto,
        EntityManagerInterface           $em,
        EmployeeRepository               $employeeRepo,
    ): JsonResponse
    {
        try {
            $employee = $employeeRepo->findOneBy(['uuid' => Uuid::fromString($dto->employeeUuid)]);

            $start = new \DateTime($dto->start);
            $stop = new \DateTime($dto->stop);

            $workStart = clone $start;
            $workStart->setTime(0, 0);

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

}
