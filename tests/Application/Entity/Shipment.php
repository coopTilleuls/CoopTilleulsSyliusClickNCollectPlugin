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

namespace Tests\CoopTilleuls\SyliusClickNCollectPlugin\Application\Entity;

use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShipment;
use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShipmentInterface;
use CoopTilleuls\SyliusClickNCollectPlugin\Validator\Constraints\SlotAvailable;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Shipment as BaseShipment;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
#[ORM\Entity]
#[ORM\Table(name: "sylius_shipment")]
#[ORM\Index(columns: ["location_id", "collection_time"])]
#[SlotAvailable(groups: ["sylius"])]
class Shipment extends BaseShipment implements ClickNCollectShipmentInterface
{
    use ClickNCollectShipment;
}
