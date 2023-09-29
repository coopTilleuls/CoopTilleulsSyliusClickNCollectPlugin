<?php

declare(strict_types=1);

namespace CoopTilleuls\SyliusClickNCollectPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface ClosedPeriodInterface extends ResourceInterface
{
    public function getId(): int;
    public function setId(int $id): void;
    public function getStartAt(): \DateTimeInterface;
    public function setStartAt(\DateTimeInterface $startAt): void;
    public function getEndAt(): ?\DateTimeInterface;
    public function setEndAt(?\DateTimeInterface $endAt): void;
    public function getLocation(): LocationInterface;
    public function setLocation(LocationInterface $location): void;
}
