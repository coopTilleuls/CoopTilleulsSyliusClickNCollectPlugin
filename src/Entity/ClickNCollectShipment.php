<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Les-Tilleuls.coop <contact@les-tilleuls.coop>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CoopTilleuls\SyliusClickNCollectPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;

trait ClickNCollectShipment
{
    /**
     * @ORM\ManyToOne(targetEntity=CoopTilleuls\SyliusClickNCollectPlugin\Entity\Place::class)
     */
    protected ?Place $place = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $pin;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected ?\DateTimeInterface $collectionTime = null;

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function isClickNCollect(): bool
    {
        return null !== $this->place && null !== $this->collectionTime;
    }

    public function setPlace(?Place $place): void
    {
        $this->place = $place;
        if (null !== $place && null === $this->pin && $place->isGeneratePin()) {
            $this->pin = sprintf('%04d', random_int(0, 9999));
        }
    }

    public function getCollectionTime(): ?\DateTimeInterface
    {
        return  $this->collectionTime;
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
