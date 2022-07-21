<?php

declare(strict_types=1);

namespace CoopTilleuls\SyliusClickNCollectPlugin\Entity;

class ClosedPeriod implements ClosedPeriodInterface
{
    protected int $id;
    protected \DateTimeInterface $startAt;
    protected ?\DateTimeInterface $endAt = null;
    protected LocationInterface $location;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getStartAt(): \DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): void
    {
        $this->startAt = $startAt;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeInterface $endAt): void
    {
        $this->endAt = $endAt;
    }

    public function getLocation(): LocationInterface
    {
        return $this->location;
    }

    public function setLocation(LocationInterface $location): void
    {
        $this->location = $location;
    }
}
