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

namespace Tests\CoopTilleuls\SyliusClickNCollectPlugin\CollectionTime;

use CoopTilleuls\SyliusClickNCollectPlugin\CollectionTime\AvailableSlotsComputer;
use CoopTilleuls\SyliusClickNCollectPlugin\Entity\Location;
use CoopTilleuls\SyliusClickNCollectPlugin\Repository\CollectionTimeRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Tests\CoopTilleuls\SyliusClickNCollectPlugin\Application\Entity\Shipment;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class AvailableSlotsComputerTest extends TestCase
{
    public function testStartAfterEnd(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $computer = new AvailableSlotsComputer($this->prophesize(CollectionTimeRepositoryInterface::class)->reveal());
        $computer->__invoke(new Shipment(), new Location(), new \DateTimeImmutable('+1 week'), new \DateTimeImmutable('now'));
    }

    public function testRecurrence(): void
    {
        $dtstart = (new \DateTimeImmutable('first day of this month 08:00 UTC'))->format('Ymd\This');

        $location = new Location();
        $location->setOrderPreparationDelay(20);
        $location->setRrule(sprintf('FREQ=MINUTELY;INTERVAL=20;BYHOUR=9,10,11,12,13,14,15,16;BYDAY=MO,TU,WE,TH,FR;DTSTART=%s;DTEND=29990316T082000', $dtstart));

        $shipment = new Shipment();
        $shipment->setCollectionTime(new \DateTimeImmutable('next monday 10:00'));
        $shipment->setLocation($location);

        $collectionTimeRepositoryProphecy = $this->prophesize(CollectionTimeRepositoryInterface::class);
        $collectionTimeRepositoryProphecy->findFullSlots(Argument::cetera())->willReturn([$shipment->getCollectionTime(), new \DateTimeImmutable('next monday 10:20')]);

        $computer = new AvailableSlotsComputer($collectionTimeRepositoryProphecy->reveal());
        $recurrences = $computer->__invoke($shipment, $location, new \DateTimeImmutable('next monday 08:00'), new \DateTimeImmutable('next monday 12:00'));

        $this->assertCount(8, $recurrences);
    }
}
