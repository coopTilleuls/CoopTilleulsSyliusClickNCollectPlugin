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

namespace CoopTilleuls\SyliusClickNCollectPlugin\Validator\Constraints;

use CoopTilleuls\SyliusClickNCollectPlugin\CollectionTime\RecurrenceInstanceFinderInterface;
use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShipmentInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Checks if this time slot is available.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class SlotAvailableValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;
    private RecurrenceInstanceFinderInterface $recurrenceInstanceFinder;

    public function __construct(EntityManagerInterface $entityManager, RecurrenceInstanceFinderInterface $recurrenceInstanceFinder)
    {
        $this->entityManager = $entityManager;
        $this->recurrenceInstanceFinder = $recurrenceInstanceFinder;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof SlotAvailableInterface) {
            throw new UnexpectedTypeException($constraint, Rrule::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof ClickNCollectShipmentInterface) {
            throw new UnexpectedValueException($value, ClickNCollectShipmentInterface::class);
        }

        if ((null === $method = $value->getMethod()) || !$method->isClickNCollect()) {
            return;
        }

        if (null === $collectionTime = $value->getCollectionTime()) {
            return;
        }

        $previousValue = $this->entityManager->getUnitOfWork()->getOriginalEntityData($value)['collectionTime'];
        if ($collectionTime == $previousValue) {
            return;
        }

        try {
            ($this->recurrenceInstanceFinder)($value);
        } catch (\RuntimeException | \InvalidArgumentException $e) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $collectionTime->format(\DateTime::ATOM))
                ->addViolation();
        }
    }
}
