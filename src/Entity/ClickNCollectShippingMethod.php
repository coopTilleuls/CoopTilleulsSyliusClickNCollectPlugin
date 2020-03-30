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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
trait ClickNCollectShippingMethod
{
    /**
     * @var Collection|Location[]
     *
     * @ORM\ManyToMany(targetEntity=\CoopTilleuls\SyliusClickNCollectPlugin\Entity\Location::class, inversedBy="shippingMethods")
     * @ORM\JoinTable(name="coop_tilleuls_click_n_collect_shipping_method_location")
     */
    protected $locations;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }

    public function isClickNCollect(): bool
    {
        return !$this->locations->isEmpty();
    }

    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(Location $location): void
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
        }

        $shippingMethods = $location->getShippingMethods();
        if (!$shippingMethods->contains($this)) {
            $shippingMethods->add($this);
        }
    }

    public function removeLocation(Location $location): void
    {
        $this->locations->removeElement($location);
        $location->getShippingMethods()->removeElement($this);
    }
}
