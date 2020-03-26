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

namespace CoopTilleuls\SyliusClickNCollectPlugin\CollectionTime;

use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShipmentInterface;
use CoopTilleuls\SyliusClickNCollectPlugin\Entity\PlaceInterface;
use CoopTilleuls\SyliusClickNCollectPlugin\Repository\CollectionTimeRepositoryInterface;
use Recurr\Recurrence;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\BetweenConstraint;

/**
 * {@inheritdoc}
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class AvailableSlotsComputer implements AvailableSlotsComputerInterface
{
    private CollectionTimeRepositoryInterface $collectionTimeRepository;

    public function __construct(CollectionTimeRepositoryInterface $collectionTimeRepository)
    {
        $this->collectionTimeRepository = $collectionTimeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ClickNCollectShipmentInterface $shipment, PlaceInterface $place, ?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null, bool $onlyFuture = true): array
    {
        $minStartDate = new \DateTimeImmutable(sprintf('+%d minutes', $place->getOrderPreparationDelay()));
        if (null === $startDate || ($onlyFuture && $startDate < $minStartDate)) {
            $startDate = $minStartDate;
        }

        if (null === $endDate) {
            $endDate = new \DateTimeImmutable('+1 week');
        } elseif ($startDate > $endDate || $startDate->diff($endDate)->days > 31) {
            throw new \InvalidArgumentException(sprintf('Invalid date range %s - %s (1 month max).', $startDate->format(\DateTime::ATOM), $endDate->format(\DateTime::ATOM)));
        }

        $fullSlots = $this->collectionTimeRepository->findFullSlots($place, $startDate, $endDate);
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
