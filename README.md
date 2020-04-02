<h1 align="center"><a href="https://click-n-collect.shop"><img src="logo.svg" alt="Sylius Click 'N' Collect"></a></h1>

**Sell and hand over securely during the coronavirus pandemic!**

Because of the coronavirus pandemic, a large part of the world is under lockdown and most shops are closed or with long queues.

**Sylius Click 'N' Collect** is a solution to sell and hand over your products **safely** during the lockdown (and even after that).
By allowing **contactless** pick up and preventing queues, **Sylius Click 'N' Collect** protects your workers and your customers.

**Sylius Click 'N' Collect** is a plugin for the [Sylius](https://sylius.com/) eCommerce platform.
[It's free](#license) (as in speech, and as in beer), and it's brought to you by your friends from [Les-Tilleuls.coop](https://les-tilleuls.coop). It can also be used in addition to all the existing features and plugins of Sylius.

![CI](https://github.com/coopTilleuls/CoopTilleulsSyliusClickNCollectPlugin/workflows/CI/badge.svg)

## How Does It Work?

[![Screencast](https://i.imgur.com/yOU3sw7.png)](https://www.youtube.com/watch?v=bQ9_vQJf-2I)

As a customer:

1. Go on the shop's website
2. Select the products to order
3. Select the collect location (e.g. the physical shop)
4. Select an available time slot
5. Pay online, or select in-store payment (Stripe and PayPal are also supported).
6. Optional: a PIN is generated, you'll need it to retrieve the order
7. Pick up your order at the shop during the selected time slot! Use the PIN code to unlock the box if appropriate.

As a seller:

1. Use the timetable in the admin interface to see the upcoming orders
2. Prepare the orders: put them in a bag and make the order number noticeable (staple it or use a marker)
3. Clean carefully the shelf and the lock (if appropriate) with the appropriate cleaning products
4. Put the bag in the shelf
5. Optional: use the PIN to lock the box in the shelf to secure the order
6. Monitor the pick up from afar, from behind a window, or using CCTV

Repeat!

You're now selling while preventing the pandemic to spread!

## Install

Note: to test the plugin locally, see [CONTRIBUTING.md](CONTRIBUTING.md)

1. [Install Sylius](https://docs.sylius.com/en/latest/book/installation/installation.html)
2. Install **Sylius Click 'N' Collect**: `composer require tilleuls/sylius-click-n-collect-plugin`
3. Register the bundle:

    ```php
    <?php
    
    // config/bundles.php

    return [
        // ...
        CoopTilleuls\SyliusClickNCollectPlugin\CoopTilleulsSyliusClickNCollectPlugin::class => ['all' => true],
    ];

4. Import the configuration:

    ```yaml
    # config/packages/sylius_click_n_collect.yaml
    imports:
        - { resource: "@CoopTilleulsSyliusClickNCollectPlugin/Resources/config/app/config.yml" }
     ```

5. Import the routes:

    ```yaml
    # config/routes/sylius_click_n_collect.yaml
    coop_tilleuls_sylius_click_n_collect_shop:
        resource: "@CoopTilleulsSyliusClickNCollectPlugin/Resources/config/shop_routing.yml"
        prefix: /{_locale}
        requirements:
            _locale: ^[a-z]{2}(?:_[A-Z]{2})?$
    
    coop_tilleuls_sylius_click_n_collect_admin:
        resource: "@CoopTilleulsSyliusClickNCollectPlugin/Resources/config/admin_routing.yml"
        prefix: /admin
    ```

6. Update the native entities:

    ```php
    <?php
    
    // src/Entity/Shipping/ShippingMethod.php
    
    namespace App\Entity\Shipping;
    
    use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShippingMethod;
    use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShippingMethodInterface;
    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;
    
    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_shipping_method")
     */
    class ShippingMethod extends BaseShippingMethod implements ClickNCollectShippingMethodInterface
    {
        use ClickNCollectShippingMethod {
            __construct as initializeShippingMethodLocations;
        }
    
        public function __construct()
        {
            parent::__construct();
    
            $this->initializeShippingMethodLocations();
        }
    
        // ...
    }
    ```

    ```php
    <?php
    
    // src/Entity/Shipping/Shipment.php
    
    namespace App\Entity\Shipping;
    
    use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShipment;
    use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShipmentInterface;
    use CoopTilleuls\SyliusClickNCollectPlugin\Validator\Constraints\SlotAvailable;
    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\Shipment as BaseShipment;
    
    /**
    * @ORM\Entity
    * @ORM\Table(name="sylius_shipment", indexes={@ORM\Index(columns={"location_id", "collection_time"})})
    * @SlotAvailable(groups={"sylius"})
    */
    class Shipment extends BaseShipment implements ClickNCollectShipmentInterface
    {
       use ClickNCollectShipment;
    }
    ```

7. Override the templates:

       cp -R vendor/tilleuls/sylius-click-n-collect-plugin/tests/Application/templates/* templates

8. Create and execute database migrations:

       bin/console doctrine:migrations:diff
       bin/console bin/console doctrine:migrations:migrate

9. Add your products and stocks or [import them](https://github.com/coopTilleuls/CoopTilleulsSyliusQuickImportPlugin)
10. Configure the pick up locations, the available time slots, and how many people you can safely serve in parallel
11. Create a dedicated shipping method
12. Optionally, configure an online payment method from the admin (Stripe and PayPal are supported out of the box)

**You're ready to sell!**

# License

## Pandemic Clause

To use this software, you **MUST** ensure that all the workers and customers using it aren't at risk!
It means that you **MUST** reduce the number of contacts between the workers and customers (in most cases the delivery should be contactless),
and that you **MUST** provide masks, hand sanitizers and gloves to the workers who handle products.

Lives matter more than profits.

## AGPL

Sylius Click 'N' Collect is licensed under [AGPL-3.0](LICENSE).
For companies not wanting, or not able to use AGPL-3.0 licensed software, commercial licenses are also available.
[Contact us for more information](mailto:contact@les-tilleuls.coop).

# Credits

Created by [Kévin Dunglas](https://dunglas.fr) for [Les-Tilleuls.coop](https://les-tilleuls.coop).
Commercial support available at [Les-Tilleuls.coop](https://les-tilleuls.coop).
