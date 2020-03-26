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

namespace CoopTilleuls\SyliusClickNCollectPlugin\EventListener;

use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShipmentInterface;
use CoopTilleuls\SyliusClickNCollectPlugin\Repository\CollectionTimeRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Exception\RaceConditionException;
use Symfony\Component\Lock\LockInterface;

/**
 * Prevents concurrent insertion of collection times.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ShipmentCollectionTimeLockListener
{
    private ManagerRegistry $managerRegistry;
    private CollectionTimeRepositoryInterface $collectionTimeRepository;
    private LockInterface $lock;
    private string $shipmentClass;

    public function __construct(ManagerRegistry $managerRegistry, LockInterface $lock, CollectionTimeRepositoryInterface $collectionTimeRepository, string $shipmentClass)
    {
        $this->managerRegistry = $managerRegistry;
        $this->collectionTimeRepository = $collectionTimeRepository;
        $this->lock = $lock;
        $this->shipmentClass = $shipmentClass;
    }

    /**
     * @throws RaceConditionException
     */
    public function onPreSelectShipping(ResourceControllerEvent $event): void
    {
        if (!$shipments = $this->getShipmentToChecks($event->getSubject())) {
            return;
        }

        $unitOfWork = $this->managerRegistry->getManagerForClass($this->shipmentClass)->getUnitOfWork();

        $this->lock->acquire(true);
        foreach ($shipments as $shipment) {
            $previousCollectionTime = $unitOfWork->getOriginalEntityData($shipment)['collectionTime'] ?? null;
            $newCollectionTime = $shipment->getCollectionTime();

            if ($previousCollectionTime !== $newCollectionTime && $this->collectionTimeRepository->isSlotFull($shipment->getPlace(), $shipment->getCollectionTime())) {
                $this->lock->release();
                throw new RaceConditionException();
            }
        }
    }

    public function onPostSelectShipping(): void
    {
        if ($this->lock->isAcquired()) {
            $this->lock->release();
        }
    }

    /**
     * @return ClickNCollectShipmentInterface[]
     */
    private function getShipmentToChecks($order): array
    {
        if (!$order instanceof OrderInterface) {
            return $order;
        }

        $filteredShipments = [];
        foreach ($order->getShipments() as $shipment) {
            if ($shipment instanceof ClickNCollectShipmentInterface && null !== $shipment->getCollectionTime()) {
                $filteredShipments[] = $shipment;
            }
        }

        return $filteredShipments;
    }
}
