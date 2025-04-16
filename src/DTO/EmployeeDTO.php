<?php
declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class EmployeeDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $surname;
}
