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

namespace Tests\CoopTilleuls\SyliusClickNCollectPlugin\E2e;

use Symfony\Component\Panther\PantherTestCase;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ShopTest extends PantherTestCase
{
    public function testOrder(): void
    {
        $client = self::createPantherClient();

        $client->request('GET', '/en_US/products/330m-slim-fit-jeans');
        $this->assertPageTitleContains('Sylius');
    }
}
