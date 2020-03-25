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

namespace CoopTilleuls\SyliusClickNCollectPlugin\Repository;

use CoopTilleuls\SyliusClickNCollectPlugin\Entity\PlaceInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

final class CollectionTimeRepository
{
    private ManagerRegistry $managerRegistry;
    private string $shipmentClass;

    public function __construct(ManagerRegistry $managerRegistry, string $shipmentClass)
    {
        $this->managerRegistry = $managerRegistry;
        $this->shipmentClass = $shipmentClass;
    }

    /**
     * @return \DateTimeInterface[]
     */
    public function findFullSlots(PlaceInterface $place, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $query = $this->managerRegistry->getManagerForClass($this->shipmentClass)->createQuery(<<<DQL
            SELECT s.collectionTime AS collection_time
            FROM {$this->shipmentClass} s
            WHERE s.place = :place
            AND s.collectionTime BETWEEN :start_date AND :end_date
            GROUP BY s.place, s.collectionTime
            HAVING COUNT(s.id) >= :throughput
            ORDER BY s.collectionTime
        DQL
        )->setParameters([
            'place' => $place,
            'start_date' => $start,
            'end_date' => $end,
            'throughput' => $place->getThroughput(),
        ]);

        return array_column($query->getArrayResult(), 'collection_time');
    }

    public function isSlotFull(PlaceInterface $place, \DateTimeInterface $collectionTime): bool
    {
        $query = $this->managerRegistry->getManagerForClass($this->shipmentClass)->createQuery(<<<DQL
            SELECT COUNT(s.collectionTime) AS c
            FROM {$this->shipmentClass} s
            WHERE s.place = :place
            AND s.collectionTime = :collection_time
            GROUP BY s.place, s.collectionTime
        DQL
        )->setParameters([
            'place' => $place,
            'collection_time' => $collectionTime,
        ]);

        return ($query->getArrayResult()[0]['c'] ?? 0) >= $place->getThroughput();
    }
}
