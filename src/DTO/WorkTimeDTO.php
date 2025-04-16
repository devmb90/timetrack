<?php
declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class WorkTimeDTO
{
    #[Assert\NotBlank]
    public string $employeeUuid;

    #[Assert\NotBlank]
    #[Assert\DateTime(format: 'Y-m-d H:i:s')]
    public string $start;

    #[Assert\NotBlank]
    #[Assert\DateTime(format: 'Y-m-d H:i:s')]
    public string $stop;

}
