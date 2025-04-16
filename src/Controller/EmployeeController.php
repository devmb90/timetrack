<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\EmployeeDTO;
use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/api')]
class EmployeeController extends AbstractController
{
    #[Route('/employees', methods: ['POST'])]
    public function createEmployee(
        #[MapRequestPayload] EmployeeDTO $dto,
        EntityManagerInterface           $em,
        SerializerInterface              $serializer
    ): JsonResponse
    {

        $employee = new Employee();
        $employee
            ->setUuid(Uuid::v4())
            ->setName($dto->name)
            ->setSurname($dto->surname);

        $em->persist($employee);
        $em->flush();

        $serializedData = $serializer->serialize($employee->getUuid(), 'json');
        return new JsonResponse($serializedData, 200, [], true);
    }
}
