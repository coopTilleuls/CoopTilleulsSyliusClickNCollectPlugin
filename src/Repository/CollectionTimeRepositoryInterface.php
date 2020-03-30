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
use CoopTilleuls\SyliusClickNCollectPlugin\Entity\Location;
use CoopTilleuls\SyliusClickNCollectPlugin\Entity\LocationInterface;

/**
 * Finds collection times relates to shipments.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface CollectionTimeRepositoryInterface
{
    /**
     * Finds slot already full according to the Location throughput.
     *
     * @return \DateTimeInterface[]
     */
    public function findFullSlots(LocationInterface $location, \DateTimeInterface $start, \DateTimeInterface $end): array;

    /**
     * Checks if a time slot is already full according to the Location throughput.
     */
    public function isSlotFull(LocationInterface $location, \DateTimeInterface $collectionTime): bool;

    /**
     * Finds all shipments with a collection time between the provided range.
     *
     * @return ClickNCollectShipmentInterface[]
     */
    public function findShipments(Location $location, \DateTimeInterface $start, \DateTimeInterface $end): array;
}
