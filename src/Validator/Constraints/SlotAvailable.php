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

use Symfony\Component\Validator\Constraint;

/**
 * Checks if a time slot is available.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 *
 * @Annotation
 */
final class SlotAvailable extends Constraint implements SlotAvailableInterface
{
    public string $message = 'The time slot "{{ value }}" is not available anymore.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'click_n_collect_slot_available';
    }
}
