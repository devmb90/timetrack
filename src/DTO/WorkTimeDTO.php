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

    public function validate()
    {
        $this->validateStartStop();
        $this->validateInterval();
    }

    private function validateStartStop(): void
    {
        $startDate = new \DateTime($this->start);
        $stopDate = new \DateTime($this->stop);

        if ($stopDate <= $startDate) {
            throw new \Exception('Invalid start date');
        }
    }

    private function validateInterval(): void
    {
        $startDate = new \DateTime($this->start);
        $stopDate = new \DateTime($this->stop);
        $interval = $stopDate->diff($startDate);

        if ($interval->h > 12 || ($interval->h === 12 && $interval->i > 0)) {
            throw new \Exception('Pracownik nie może zarejestrować więcej niż 12 godzin w jednym
przedziale');
        }
    }
}
