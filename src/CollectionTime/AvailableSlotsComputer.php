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
use CoopTilleuls\SyliusClickNCollectPlugin\Entity\LocationInterface;
use CoopTilleuls\SyliusClickNCollectPlugin\Repository\CollectionTimeRepositoryInterface;
use Recurr\Recurrence;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\ArrayTransformerConfig;
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
    public function __invoke(ClickNCollectShipmentInterface $shipment, LocationInterface $location, ?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null, bool $onlyFuture = true, int $limit = 732): array
    {
        $ruleStartDate = $startDate;
        $minStartDate = new \DateTimeImmutable(sprintf('+%d minutes', $location->getOrderPreparationDelay()));
        if (null === $startDate || ($onlyFuture && $startDate < $minStartDate)) {
            $startDate = $minStartDate;
        }

        if (null === $endDate) {
            $endDate = new \DateTimeImmutable('+1 week');
        } elseif ($startDate > $endDate || $startDate->diff($endDate)->days > 31) {
            throw new \InvalidArgumentException(sprintf('Invalid date range %s - %s (1 month max).', $startDate->format(\DateTime::ATOM), $endDate->format(\DateTime::ATOM)));
        }

        $rule = new Rule($location->getRrule());

        if ($ruleStartDate) {
            $ruleEndDate = $ruleStartDate->add($rule->getStartDate()->diff($rule->getEndDate()));
            $rule->setStartDate($ruleStartDate);
            $rule->setEndDate(\DateTime::createFromImmutable($ruleEndDate));
        }

        $fullSlots = $this->collectionTimeRepository->findFullSlots($location, $startDate, $endDate);
        $recurrences = (new ArrayTransformer((new ArrayTransformerConfig())->setVirtualLimit($limit)))->transform(
            $rule,
            new BetweenConstraint($startDate, $endDate)
        );

        $sameLocation = $location === $shipment->getLocation();
        $currentCollectionTime = $shipment->getCollectionTime();

        return array_filter($recurrences->toArray(), function (Recurrence $r) use ($sameLocation, $currentCollectionTime, $fullSlots) {
            // Keep only the current collection time and available slots
            $start = $r->getStart();

            return ($sameLocation && $currentCollectionTime == $start) || !\in_array($start, $fullSlots, false);
        });
    }
}
