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

namespace CoopTilleuls\SyliusClickNCollectPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $configuration = $menu->getChild('configuration');

        $configuration
            ->addChild('place', ['route' => 'coop_tilleuls_click_n_collect_admin_place_index'])
            ->setLabel('coop_tilleuls_click_n_collect.menu.admin.main.configuration.place')
            ->setLabelAttribute('icon', 'map marker alternate');

        $keys = array_keys($configuration->getChildren());
        $shippingCategoriesIndex = array_search('shipping_categories', $keys, true);

        $configuration->reorderChildren(array_merge(\array_slice($keys, 0, $shippingCategoriesIndex + 1), ['place'] + \array_slice($keys, $shippingCategoriesIndex, \count($keys) - $shippingCategoriesIndex - 1)));
    }
}
