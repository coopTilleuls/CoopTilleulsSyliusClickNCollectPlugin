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

namespace CoopTilleuls\SyliusClickNCollectPlugin\CollectionTime;

use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollecttShipmentInterface;
use CoopTilleuls\SyliusClickNCollectPlugin\Entity\PlaceInterface;
use CoopTilleuls\SyliusClickNCollectPlugin\Repository\CollectionTimeRepository;
use Recurr\Recurrence;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\BetweenConstraint;

final class AvailableCollectionTimesComputer
{
    private CollectionTimeRepository $collectionTimeRepository;

    public function __construct(CollectionTimeRepository $collectionTimeRepository)
    {
        $this->collectionTimeRepository = $collectionTimeRepository;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function __invoke(ClickNCollecttShipmentInterface $shipment, PlaceInterface $place, ?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): array
    {
        $minStartDate = new \DateTimeImmutable(sprintf('+%d minutes', $place->getOrderPreparationDelay()));
        if (null === $startDate || $startDate < $minStartDate) {
            $startDate = $minStartDate;
        }

        if (null === $endDate) {
            $endDate = new \DateTimeImmutable('+1 week');
        } elseif ($startDate > $endDate || $startDate->diff($endDate)->days > 31) {
            throw new \InvalidArgumentException('The end date cannot be more than one month after the start date');
        }

        $fullSlots = $this->collectionTimeRepository->findFullSlots($place, $startDate, $endDate, $place->getThroughput());
        $recurrences = (new ArrayTransformer())->transform(
            new Rule($place->getRrule()),
            new BetweenConstraint($startDate, $endDate)
        );

        $samePlace = $place === $shipment->getPlace();
        $currentCollectionTime = $shipment->getCollectionTime();

        return array_filter($recurrences->toArray(), function (Recurrence $r) use ($samePlace, $currentCollectionTime, $fullSlots) {
            // Keep only the current collection time and available slots
            $start = $r->getStart();

            return ($samePlace && $currentCollectionTime == $start) || !\in_array($start, $fullSlots, false);
        });
    }
}
