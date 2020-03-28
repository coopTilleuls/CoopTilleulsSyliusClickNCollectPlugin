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

namespace CoopTilleuls\SyliusClickNCollectPlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class PlaceFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'place';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->scalarNode('street')->end()
                ->scalarNode('city')->end()
                ->scalarNode('postcode')->end()
                ->scalarNode('country_code')->end()
                ->scalarNode('province_code')->end()
                ->scalarNode('province_name')->end()
                ->scalarNode('rrule')->cannotBeEmpty()->end()
                ->integerNode('order_preparation_delay')->end()
                ->integerNode('throughput')->end()
                ->booleanNode('generate_pin')->end()
                ->arrayNode('shipping_methods')->scalarPrototype()->end()->end();
    }
}
