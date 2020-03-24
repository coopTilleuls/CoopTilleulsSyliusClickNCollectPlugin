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

use Sylius\Component\Shipping\Model\ShipmentInterface;

interface ClickNCollecttShipmentInterface extends ShipmentInterface
{
    public function isClickNCollect(): bool;

    public function getPlace(): ?Place;

    public function setPlace(?Place $place): void;

    public function getCollectionTime(): ?\DateTimeInterface;

    public function setCollectionTime(?\DateTimeInterface $collectionTime);

    public function getPin(): ?string;

    public function setPin(?string $pin): void;
}
