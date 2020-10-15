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

namespace Tests\CoopTilleuls\SyliusClickNCollectPlugin\Validator\Constraints;

use CoopTilleuls\SyliusClickNCollectPlugin\CollectionTime\RecurrenceInstanceFinderInterface;
use CoopTilleuls\SyliusClickNCollectPlugin\Entity\Location;
use CoopTilleuls\SyliusClickNCollectPlugin\Validator\Constraints\SlotAvailable;
use CoopTilleuls\SyliusClickNCollectPlugin\Validator\Constraints\SlotAvailableValidator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Prophecy\Argument;
use Recurr\Recurrence;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Tests\CoopTilleuls\SyliusClickNCollectPlugin\Application\Entity\Shipment;
use Tests\CoopTilleuls\SyliusClickNCollectPlugin\Application\Entity\ShippingMethod;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class SlotAvailableValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): SlotAvailableValidator
    {
        $unitOfWorkProphecy = $this->prophesize(UnitOfWork::class);
        $unitOfWorkProphecy->getOriginalEntityData(Argument::type(Shipment::class))->willReturn(['collectionTime' => new \DateTimeImmutable('2019-01-01', new \DateTimeZone('UTC'))]);

        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $entityManager->getUnitOfWork()->willReturn($unitOfWorkProphecy->reveal());

        $recurrenceInstanceFinderProphecy = $this->prophesize(RecurrenceInstanceFinderInterface::class);
        $recurrenceInstanceFinderProphecy->__invoke(Argument::that(function (Shipment $s) {
            return $s->getCollectionTime() == new \DateTimeImmutable('2019-01-03', new \DateTimeZone('UTC'));
        }))->willThrow(new \InvalidArgumentException());
        $recurrenceInstanceFinderProphecy->__invoke(Argument::type(Shipment::class))->willReturn(new Recurrence());

        return new SlotAvailableValidator(
            $entityManager->reveal(),
            $recurrenceInstanceFinderProphecy->reveal(),
            Shipment::class
        );
    }

    /**
     * @dataProvider getValidValues
     */
    public function testValidValues(?Shipment $value): void
    {
        $this->validator->validate($value, new SlotAvailable());
        $this->assertNoViolation();
    }

    public function getValidValues(): iterable
    {
        yield [null];

        $s1 = new Shipment();
        yield [$s1];

        $s2 = new Shipment();
        $s2->setCollectionTime(new \DateTimeImmutable('2019-01-01', new \DateTimeZone('UTC')));
        yield [$s2];

        $s3 = new Shipment();
        $s3->setCollectionTime(new \DateTimeImmutable('2019-01-02', new \DateTimeZone('UTC')));
        yield [$s3];
    }

    public function testInvalidValues(): void
    {
        $ct = new \DateTimeImmutable('2019-01-03', new \DateTimeZone('UTC'));

        $sm = new ShippingMethod();
        $sm->addLocation(new Location());
        $s = new Shipment();
        $s->setCollectionTime($ct);
        $s->setMethod($sm);

        $constraint = new SlotAvailable();
        $constraint->message = 'myMessage';
        $this->validator->validate($s, $constraint);

        $this
            ->buildViolation('myMessage')
            ->setParameter('{{ value }}', $ct->format(\DateTime::ATOM))
            ->assertRaised();
    }

    public function testUnexpectedType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(new Shipment(), new NotBlank());
    }

    public function testUnexpectedValue(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(new Location(), new SlotAvailable());
    }
}
