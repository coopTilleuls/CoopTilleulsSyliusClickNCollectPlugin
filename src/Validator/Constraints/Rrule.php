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
 * Is this string a valid iCalendar RRULE (RFC 5545)?
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
#[\Attribute]
final class Rrule extends Constraint
{
    public string $message = 'The string "{{ value }}" is not a valid iCalendar recurrence rule (RFC 5545).';
}
