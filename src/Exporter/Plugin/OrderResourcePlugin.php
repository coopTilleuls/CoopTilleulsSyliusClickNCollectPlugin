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

namespace CoopTilleuls\SyliusClickNCollectPlugin\Exporter\Plugin;

use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShipmentInterface;
use FriendsOfSylius\SyliusImportExportPlugin\Exporter\Plugin\OrderResourcePlugin as BaseOrderResourcePlugin;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\Shipment;

class OrderResourcePlugin extends BaseOrderResourcePlugin
{
    public function init(array $idsToExport): void
    {
        parent::init($idsToExport);
        foreach ($this->resources as $resource) {
            $this->addClickNCollect($resource);
        }
    }

    private function addClickNCollect(OrderInterface $resource): void
    {
        $resource->getShipments()->forAll(function ($key, Shipment $shipment) use ($resource): void {
            $isClickNCollect = $shipment instanceof ClickNCollectShipmentInterface;
            if (true === $isClickNCollect && true === $shipment->isClickNCollect()) {
                $location = $shipment->getLocation();
                $this->addDataForResource($resource, 'ClickNCollect_Location_Name', $location->getName());
                $this->addDataForResource($resource, 'ClickNCollect_Location_Countrycode', $location->getCountryCode());
                $this->addDataForResource($resource, 'ClickNCollect_Location_Street', $location->getStreet());
                $this->addDataForResource($resource, 'ClickNCollect_Location_City', $location->getCity());
                $this->addDataForResource($resource, 'ClickNCollect_Location_PostCode', $location->getPostcode());
                $this->addDataForResource($resource, 'ClickNCollect_Pin', $shipment->getPin());
                $this->addDataForResource($resource, 'ClickNCollect_CollectionTime',
                    $shipment->getCollectionTime() instanceof \DateTimeInterface ? $shipment->getCollectionTime()->format(\DateTimeInterface::ISO8601) : null);
            }
        });
    }
}
