<?php

declare(strict_types=1);

namespace CoopTilleuls\SyliusClickNCollectPlugin\Form\Type;

use CoopTilleuls\SyliusClickNCollectPlugin\Entity\ClosedPeriod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClosedPeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startAt', DateType::class, [
                'label' => 'sylius.ui.start_date',
                'widget' => 'single_text',
                'empty_data' => null,
                'constraints' => [
                    new NotBlank(['groups' => ['sylius']]),
                ]
            ])
            ->add('endAt', DateType::class, [
                'required' => false,
                'label' => 'sylius.ui.end_date',
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ClosedPeriod::class,
        ]);
    }
}
