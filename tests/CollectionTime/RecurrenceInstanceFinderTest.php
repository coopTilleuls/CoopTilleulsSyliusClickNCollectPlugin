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

use CoopTilleuls\SyliusClickNCollectPlugin\CollectionTime\AvailableSlotsComputerInterface;
use CoopTilleuls\SyliusClickNCollectPlugin\CollectionTime\RecurrenceInstanceFinder;
use CoopTilleuls\SyliusClickNCollectPlugin\Entity\Location;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Recurr\Recurrence;
use Tests\CoopTilleuls\SyliusClickNCollectPlugin\Application\Entity\Shipment;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class RecurrenceInstanceFinderTest extends TestCase
{
    public function testNoCollectionTime(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $recurrenceInstanceFinder = new RecurrenceInstanceFinder(
            $this->prophesize(AvailableSlotsComputerInterface::class)->reveal()
        );
        $recurrenceInstanceFinder->__invoke(new Shipment());
    }

    public function testNoLocations(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $recurrenceInstanceFinder = new RecurrenceInstanceFinder(
            $this->prophesize(AvailableSlotsComputerInterface::class)->reveal()
        );

        $shipment = new Shipment();
        $shipment->setCollectionTime(new \DateTimeImmutable());
        $recurrenceInstanceFinder->__invoke($shipment);
    }

    public function testNotFound(): void
    {
        $this->expectException(\RuntimeException::class);

        $computerProphecy = $this->prophesize(AvailableSlotsComputerInterface::class);
        $computerProphecy->__invoke(Argument::cetera())->willReturn([]);

        $recurrenceInstanceFinder = new RecurrenceInstanceFinder($computerProphecy->reveal());

        $shipment = new Shipment();
        $shipment->setCollectionTime(new \DateTimeImmutable());
        $shipment->setLocation(new Location());

        $recurrenceInstanceFinder->__invoke($shipment);
    }

    public function testNotMatch(): void
    {
        $this->expectException(\RuntimeException::class);

        $start = new \DateTimeImmutable();
        $end = $start->add(new \DateInterval('PT10M'));

        $computerProphecy = $this->prophesize(AvailableSlotsComputerInterface::class);
        $computerProphecy->__invoke(Argument::cetera())->willReturn([new Recurrence($start->add(new \DateInterval('PT1S')), $end)]);

        $recurrenceInstanceFinder = new RecurrenceInstanceFinder($computerProphecy->reveal());

        $shipment = new Shipment();
        $shipment->setCollectionTime($start);
        $shipment->setLocation(new Location());

        $recur = $recurrenceInstanceFinder->__invoke($shipment);
        $this->assertSame($start, $recur->getStart());
        $this->assertSame($end, $recur->getEnd());
    }

    public function testFound(): void
    {
        $start = new \DateTimeImmutable();
        $end = $start->add(new \DateInterval('PT10M'));

        $computerProphecy = $this->prophesize(AvailableSlotsComputerInterface::class);
        $computerProphecy->__invoke(Argument::cetera())->willReturn([new Recurrence($start, $end)]);

        $recurrenceInstanceFinder = new RecurrenceInstanceFinder($computerProphecy->reveal());

        $shipment = new Shipment();
        $shipment->setCollectionTime($start);
        $shipment->setLocation(new Location());

        $recur = $recurrenceInstanceFinder->__invoke($shipment);
        $this->assertSame($start, $recur->getStart());
        $this->assertSame($end, $recur->getEnd());
    }
}
