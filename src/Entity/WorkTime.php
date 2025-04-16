<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\WorkTimeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkTimeRepository::class)]
class WorkTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'workTimes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $stop = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $workTimeStart = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getStop(): ?\DateTimeInterface
    {
        return $this->stop;
    }

    public function setStop(\DateTimeInterface $stop): static
    {
        $this->stop = $stop;

        return $this;
    }

    public function getWorkTimeStart(): ?\DateTimeInterface
    {
        return $this->workTimeStart;
    }

    public function setWorkTimeStart(\DateTimeInterface $workTimeStart): static
    {
        $this->workTimeStart = $workTimeStart;

        return $this;
    }
}
