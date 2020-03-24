<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Les-Tilleuls.coop <contact@les-tilleuls.coop>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\CoopTilleuls\SyliusClickNCollectPlugin\Application\Entity;

use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShippingMethod;
use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShippingMethodeInterface;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;
use Sylius\Component\Shipping\Model\ShippingMethodTranslation;
use Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_shipping_method")
 */
class ShippingMethod extends BaseShippingMethod implements ClickNCollectShippingMethodeInterface
{
    use ClickNCollectShippingMethod {
        __construct as initializeShippingMethodPlaces;
    }

    protected function createTranslation(): ShippingMethodTranslationInterface
    {
        return new ShippingMethodTranslation();
    }

    public function __construct()
    {
        parent::__construct();

        $this->initializeShippingMethodPlaces();
    }
}