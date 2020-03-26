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

namespace CoopTilleuls\SyliusClickNCollectPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

/**
 * Adds Click 'N' Collect entries to the admin's menu.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $sales = $menu->getChild('sales');
        $sales
            ->addChild('collections', ['route' => 'coop_tilleuls_sylius_click_n_collect_admin_collections'])
            ->setLabel('coop_tilleuls_click_n_collect.menu.admin.main.sales.collections')
            ->setLabelAttribute('icon', 'box');

        $configuration = $menu->getChild('configuration');

        $configuration
            ->addChild('places', ['route' => 'coop_tilleuls_click_n_collect_admin_place_index'])
            ->setLabel('coop_tilleuls_click_n_collect.menu.admin.main.configuration.place')
            ->setLabelAttribute('icon', 'map marker alternate');

        $keys = array_keys($configuration->getChildren());
        $shippingCategoriesIndex = array_search('shipping_categories', $keys, true);

        $configuration->reorderChildren(array_merge(\array_slice($keys, 0, $shippingCategoriesIndex + 1), ['places'] + \array_slice($keys, $shippingCategoriesIndex, \count($keys) - $shippingCategoriesIndex - 1)));
    }
}
