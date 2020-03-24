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

namespace CoopTilleuls\SyliusClickNCollectPlugin\Form\Type;

use Sylius\Bundle\AddressingBundle\Form\Type\CountryCodeChoiceType;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class PlaceType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('name', TextType::class, [
                'empty_data' => '',
                'label' => 'coop_tilleuls_click_n_collect.form.place.name',
            ])
            ->add('rrule', TextType::class, [
                'empty_data' => '',
                'label' => 'coop_tilleuls_click_n_collect.form.place.rrule',
            ])
            ->add('orderPreparationDelay', IntegerType::class, [
                'attr' => ['min' => 0],
                'label' => 'coop_tilleuls_click_n_collect.form.place.order_preparation_delay',
            ])
            ->add('throughput', IntegerType::class, [
                'attr' => ['min' => 1],
                'label' => 'coop_tilleuls_click_n_collect.form.place.throughput',
            ])
            ->add('generatePin', CheckboxType::class, [
                'required' => false,
                'label' => 'coop_tilleuls_click_n_collect.form.place.generate_pin',
            ])
            ->add('street', TextType::class, [
                'required' => false,
                'label' => 'coop_tilleuls_click_n_collect.form.place.street',
            ])
            ->add('city', TextType::class, [
                'required' => false,
                'label' => 'coop_tilleuls_click_n_collect.form.place.city',
            ])
            ->add('postcode', TextType::class, [
                'required' => false,
                'label' => 'coop_tilleuls_click_n_collect.form.place.postcode',
            ])
            ->add('countryCode', CountryCodeChoiceType::class, [
                'required' => false,
                'label' => 'coop_tilleuls_click_n_collect.form.place.country',
                'enabled' => true,
            ])
            ->add('provinceCode', TextType::class, [
                'required' => false,
                'label' => 'coop_tilleuls_click_n_collect.form.place.province_code',
            ])
            ->add('provinceName', TextType::class, [
                'required' => false,
                'label' => 'coop_tilleuls_click_n_collect.form.place.province_name',
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'label' => 'coop_tilleuls_click_n_collect.form.place.enabled',
            ]);
    }
}
