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

use CoopTilleuls\SyliusClickNCollectPlugin\Entity\Location;
use CoopTilleuls\SyliusClickNCollectPlugin\Entity\LocationInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * {@inheritdoc}
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class CollectionTimeRepository implements CollectionTimeRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private string $shipmentClass;

    public function __construct(EntityManagerInterface $entityManager, string $shipmentClass)
    {
        $this->entityManager = $entityManager;
        $this->shipmentClass = $shipmentClass;
    }

    /**
     * {@inheritdoc}
     *
     * @return \DateTimeInterface[]
     */
    public function findFullSlots(LocationInterface $location, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $query = $this->entityManager->createQuery(<<<DQL
            SELECT s.collectionTime AS collection_time
            FROM {$this->shipmentClass} s
            WHERE s.location = :location
            AND s.collectionTime BETWEEN :start_date AND :end_date
            GROUP BY s.location, s.collectionTime
            HAVING COUNT(s.id) >= :throughput
            ORDER BY s.collectionTime
        DQL
        )->setParameters([
            'location' => $location,
            'start_date' => $start,
            'end_date' => $end,
            'throughput' => $location->getThroughput(),
        ]);

        return array_column($query->getArrayResult(), 'collection_time');
    }

    /**
     * {@inheritdoc}
     */
    public function isSlotFull(LocationInterface $location, \DateTimeInterface $collectionTime): bool
    {
        $query = $this->entityManager->createQuery(<<<DQL
            SELECT COUNT(s.collectionTime) AS c
            FROM {$this->shipmentClass} s
            WHERE s.location = :location
            AND s.collectionTime = :collection_time
            GROUP BY s.location, s.collectionTime
        DQL
        )->setParameters([
            'location' => $location,
            'collection_time' => $collectionTime,
        ]);

        return ($query->getArrayResult()[0]['c'] ?? 0) >= $location->getThroughput();
    }

    /**
     * {@inheritdoc}
     */
    public function findShipments(Location $location, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->entityManager->createQuery(<<<DQL
            SELECT s
            FROM {$this->shipmentClass} s
            WHERE s.state = 'ready'
            AND s.location = :location
            AND s.collectionTime BETWEEN :start_date AND :end_date
        DQL
        )->setParameters([
            'location' => $location,
            'start_date' => $start,
            'end_date' => $end,
        ])->getResult();
    }
}
