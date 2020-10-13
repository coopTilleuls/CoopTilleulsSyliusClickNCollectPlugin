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

namespace CoopTilleuls\SyliusClickNCollectPlugin\Fixture\Factory;

use CoopTilleuls\SyliusClickNCollectPlugin\Entity\Location;
use Faker\Factory;
use Faker\Generator;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class LocationExampleFactory extends AbstractExampleFactory
{
    private FactoryInterface $locationFactory;
    private Generator $faker;
    private OptionsResolver $optionsResolver;
    private ShippingMethodRepositoryInterface $shippingMethodRepository;

    public function __construct(FactoryInterface $locationFactory, ShippingMethodRepositoryInterface $shippingMethodRepository)
    {
        $this->locationFactory = $locationFactory;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', function (): string {
                return $this->faker->sentence;
            })
            ->setDefault('code', function (Options $options): string {
                return StringInflector::nameToCode($options['name']);
            })
            ->setDefault('street', function (): string {
                return $this->faker->streetAddress;
            })
            ->setDefault('city', function (): string {
                return $this->faker->city;
            })
            ->setDefault('postcode', function (): string {
                return $this->faker->postcode;
            })
            ->setDefault('country_code', function (): string {
                return $this->faker->countryCode;
            })
            ->setDefault('province_code', null)
            ->setDefault('province_name', null)
            ->setDefault('rrule', 'FREQ=MINUTELY;INTERVAL=20;BYHOUR=9,10,11,12,13,14,15,16;BYDAY=MO,TU,WE,TH,FR;DTSTART=20200328T080000')
            ->setDefault('order_preparation_delay', function (): int {
                return $this->faker->numberBetween(0, 1440);
            })
            ->setDefault('throughput', function (): int {
                return $this->faker->numberBetween(1, 20);
            })
            ->setDefault('generate_pin', function (): bool {
                return $this->faker->boolean;
            })
            ->setDefault('shipping_methods', LazyOption::randomOnes($this->shippingMethodRepository, 1))
            ->setAllowedTypes('shipping_methods', 'array')
            ->setNormalizer('shipping_methods', LazyOption::findBy($this->shippingMethodRepository, 'code'));
    }

    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /**
         * @var Location
         */
        $location = $this->locationFactory->createNew();
        $location->setName($options['name']);
        $location->setCode($options['code']);
        $location->setStreet($options['street']);
        $location->setCity($options['city']);
        $location->setPostcode($options['postcode']);
        $location->setCountryCode($options['country_code']);
        $location->setProvinceCode($options['province_code']);
        $location->setProvinceName($options['province_name']);
        $location->setRrule($options['rrule']);
        $location->setOrderPreparationDelay($options['order_preparation_delay']);
        $location->setThroughput($options['throughput']);
        $location->setGeneratePin($options['generate_pin']);
        foreach ($options['shipping_methods'] as $shippingMethod) {
            $location->addShippingMethod($shippingMethod);
        }

        return $location;
    }
}
