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

namespace CoopTilleuls\SyliusClickNCollectPlugin\Repository;

use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShipmentInterface;
use CoopTilleuls\SyliusClickNCollectPlugin\Entity\Place;
use CoopTilleuls\SyliusClickNCollectPlugin\Entity\PlaceInterface;

/**
 * Finds collection times relates to shipments.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface CollectionTimeRepositoryInterface
{
    /**
     * Finds slot already full according to the Place throughput.
     *
     * @return \DateTimeInterface[]
     */
    public function findFullSlots(PlaceInterface $place, \DateTimeInterface $start, \DateTimeInterface $end): array;

    /**
     * Checks if a time slot is already full according to the Place throughput.
     */
    public function isSlotFull(PlaceInterface $place, \DateTimeInterface $collectionTime): bool;

    /**
     * Finds all shipments with a collection time between the provided range.
     *
     * @return ClickNCollectShipmentInterface[]
     */
    public function findShipments(Place $place, \DateTimeInterface $start, \DateTimeInterface $end): array;
}
