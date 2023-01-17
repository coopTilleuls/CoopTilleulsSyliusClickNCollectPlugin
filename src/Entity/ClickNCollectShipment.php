<?php

/*
 * This file is part of Les-Tilleuls.coop's Click 'N' Collect project.
 *
 * (c) Les-Tilleuls.coop <contact@les-tilleuls.coop>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CoopTilleuls\SyliusClickNCollectPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
trait ClickNCollectShipment
{
    #[ORM\ManyToOne(targetEntity: LocationInterface::class)]
    protected ?LocationInterface $location = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $pin = null;

    #[ORM\Column(name: 'collection_time', type: 'datetime_immutable', nullable: true)]
    #[Assert\GreaterThan('now', groups: ['sylius'])]
    protected ?\DateTimeInterface $collectionTime = null;

    public function getLocation(): ?LocationInterface
    {
        return $this->location;
    }

    public function isClickNCollect(): bool
    {
        return null !== $this->location && null !== $this->collectionTime;
    }

    public function setLocation(?LocationInterface $location): void
    {
        $this->location = $location;
        if (null !== $location && null === $this->pin && $location->isGeneratePin()) {
            $this->pin = sprintf('%04d', random_int(0, 9999));
        }
    }

    public function getCollectionTime(): ?\DateTimeInterface
    {
        return $this->collectionTime;
    }

    public function setCollectionTime(?\DateTimeInterface $collectionTime): void
    {
        $this->collectionTime = $collectionTime;
    }

    public function getPin(): ?string
    {
        return $this->pin;
    }

    public function setPin(?string $pin): void
    {
        $this->pin = $pin;
    }
}
