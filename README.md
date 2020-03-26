# Les-Tilleuls.coop's SyliusClickNCollectPlugin: Sell and deliver securely during the COVID-19 pandemic!

**Les-Tilleuls.coop Click and Collect** allows to transform your physical shop in an eCommerce shop in minutes, and to allow your customer to
pickup their orders while not putting them or your workers at risk!

**Les-Tilleuls.coop Click and Collect** is a free software plugin for the [Sylius](https://sylius.com/) eCommerce platform.

# Install

Note: to test the plugin locally, see [CONTRIBUTING.md](CONTRIBUTING.md)

1. [Install Sylius](https://docs.sylius.com/en/latest/book/installation/installation.html)
2. Install SyliusClickNCollect: `composer require tilleuls/sylius-click-n-collect-plugin`
3. Update the native entities:

    ```php
    <?php
    
    // src/Entity/Shipping/ShippingMethod.php
    
    namespace App\Entity\Shipping\ShippingMethod;
    
    use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShippingMethod;
    use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShippingMethodeInterface;
    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;
    
    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_shipping_method")
     */
    class ShippingMethod extends BaseShippingMethod implements ClickNCollectShippingMethodeInterface
    {
        use ClickNCollectShippingMethod {
            __construct as initializeShippingMethodPlaces;
        }
    
        public function __construct()
        {
            parent::__construct();
    
            $this->initializeShippingMethodPlaces();
        }
    
        // ...
    }
    ```

    ```php
    <?php
    
    // src/Entity/Shipping/ShippingMethod.php
    
    namespace App\Entity;
    
    use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShipment;
    use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClickNCollectShipmentInterface;
    use CoopTilleuls\SyliusClickNCollectPlugin\Validator\Constraints\SlotAvailable;
    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\Shipment as BaseShipment;
    
    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_shipment")
     *
     * @SlotAvailable(groups={"sylius"})
     */
    class Shipment extends BaseShipment implements ClickNCollectShipmentInterface
    {
        use ClickNCollectShipment;
    }
    ```

4. Override the templates:

       cp -R vendor/tilleuls/SyliusClickNCollectPlugin/tests/Application/templates/* templates/

5. Create and execute database migrations
6. Create your Click and Collect places and create a dedicated shipping method from the admin panel

## Without Symfony Flex

These extra steps are only necessary if **you don't use Symfony Flex**.

1. Import the configuration:
    
    ```yaml
   # config/packages/sylius_click_n_collect.yaml
   imports:
       # ...
       - { resource: "@CoopTilleulsSyliusClickNCollectPlugin/Resources/config/app/config.yml" }
    ```
2. Import the routes:

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

# License

## Pandemic Clause

To use this software, you **MUST** ensure that all the workers and customers using it aren't at risk!
It means that you **MUST** reduce the number of contacts between the workers and customers (in most cases the delivery should be contactless),
and that you **MUST** provide masks, hand sanitizers and gloves to the workers who handle products.

Lives matter more than profits.

## AGPL

Les-Tilleuls.coop Click and Collect is licensed under [AGPL-3.0](LICENSE).
For companies not wanting, or not able to use AGPL-3.0 licensed software, commercial licenses are also available. [Contact us for more information](mailto:contact@les-tilleuls.coop).

# Credits

Created by [Kévin Dunglas](https://dunglas.fr) for [Les-Tilleuls.coop](https://les-tilleuls.coop).
Commercial support available at [Les-Tilleuls.coop](https://les-tilleuls.coop).
