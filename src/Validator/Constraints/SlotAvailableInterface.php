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

/**
 * Checks if a time slot is available.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface SlotAvailableInterface
{
    /**
     * Returns whether the constraint can be put onto classes, properties or
     * both.
     *
     * This method should return one or more of the constants
     * Constraint::CLASS_CONSTRAINT and Constraint::PROPERTY_CONSTRAINT.
     *
     * @return string|array One or more constant values
     */
    public function getTargets();

    /**
     * Returns the name of the class that validates this constraint.
     *
     * By default, this is the fully qualified name of the constraint class
     * suffixed with "Validator". You can override this method to change that
     * behavior.
     *
     * @return string
     */
    public function validatedBy(): string;
}
