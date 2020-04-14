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

namespace CoopTilleuls\SyliusClickNCollectPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface ClickNCollectShippingMethodInterface extends ShippingMethodInterface
{
    public function isClickNCollect(): bool;

    /**
     * @return LocationInterface[]|Collection
     */
    public function getLocations(): Collection;

    public function addLocation(LocationInterface $location): void;

    public function removeLocation(LocationInterface $location): void;
}
