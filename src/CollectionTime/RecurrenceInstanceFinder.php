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
use Recurr\Recurrence;

/**
 * {@inheritdoc}
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class RecurrenceInstanceFinder implements RecurrenceInstanceFinderInterface
{
    private AvailableSlotsComputerInterface $computer;

    public function __construct(AvailableSlotsComputerInterface $computer)
    {
        $this->computer = $computer;
    }

    public function __invoke(ClickNCollectShipmentInterface $shipment): Recurrence
    {
        if (null === $collectionTime = $shipment->getCollectionTime()) {
            throw new \InvalidArgumentException('This shipment has no associated collection time.');
        }
        if (null === $location = $shipment->getLocation()) {
            throw new \InvalidArgumentException('This shipment has no associated location.');
        }

        foreach (($this->computer)($shipment, $location, $collectionTime->sub(new \DateInterval('PT12H')), $collectionTime->add(new \DateInterval('PT12H')), false) as $recurrence) {
            if ($collectionTime == $recurrence->getStart()) {
                return $recurrence;
            }
        }

        throw new \RuntimeException('This collection time isn\'t part of the recurrence.');
    }
}
