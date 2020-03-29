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
    /**
     * @group e2e
     */
    public function testOrder(): void
    {
        $client = self::createPantherClient();

        $crawler = $client->request('GET', '/');
        $this->assertPageTitleContains('Sylius');

        $client->click($crawler->filter('a.sylius-product-name')->link());

        $client->submitForm('Add to cart');
        $crawler = $client->waitFor('a.button.primary[href="/en_US/checkout/"]');
        $crawler = $client->click($crawler->filter('a.button.primary[href="/en_US/checkout/"]')->eq(1)->link());
        $client->waitFor('#sylius_checkout_address_customer_email');
        $form = $crawler->filter('form[name="sylius_checkout_address"]')->form();
        $form->setValues([
            'sylius_checkout_address[customer][email]' => 'dunglas@gmail.com',
            'sylius_checkout_address[billingAddress][firstName]' => 'Kévin',
            'sylius_checkout_address[billingAddress][lastName]' => 'Dunglas',
            'sylius_checkout_address[billingAddress][street]' => '82 Rue Winston Churchill',
            'sylius_checkout_address[billingAddress][city]' => 'Lomme',
            'sylius_checkout_address[billingAddress][postcode]' => '59160',
            'sylius_checkout_address[billingAddress][countryCode]' => 'FR',
        ]);

        $client->submit($form);

        // I've no clue of why it doesn't work using the Selenium API
        $client->executeScript(<<<JS
            document.evaluate('//label[text()="Click \'N\' Collect"]', document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.click();
        JS);

        $client->waitFor('.fc-event');
        $client->executeScript(<<<JS
            document.querySelector('.fc-event').click();
        JS);

        $client->submitForm('Next'); // Shipping
        $client->submitForm('Next'); // Payment

        $client->waitFor('form[name=sylius_checkout_complete]');
        $client->submitForm('Place order');

        $client->waitFor('#sylius-thank-you');
        $this->assertSelectorTextContains('#sylius-thank-you', 'Thank you!');
    }
}
