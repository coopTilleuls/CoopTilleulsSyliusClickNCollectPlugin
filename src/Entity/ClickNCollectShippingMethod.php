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
     * @var Collection|Place[]
     *
     * @ORM\ManyToMany(targetEntity=\CoopTilleuls\SyliusClickNCollectPlugin\Entity\Place::class)
     * @ORM\JoinTable(name="coop_tilleuls_click_n_collect_shipping_method_place")
     */
    protected $places;

    public function __construct()
    {
        $this->places = new ArrayCollection();
    }

    public function isClickNCollect(): bool
    {
        return !$this->places->isEmpty();
    }

    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): void
    {
        if (!$this->places->contains($place)) {
            $this->places[] = $place;
        }
    }

    public function removePlace(Place $place): void
    {
        $this->places->remove($place);
    }
}
