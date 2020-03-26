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

namespace CoopTilleuls\SyliusClickNCollectPlugin\Form\Extension;

use CoopTilleuls\SyliusClickNCollectPlugin\Entity\Place;
use Doctrine\Persistence\ObjectRepository;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\ShipmentType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class ShipmentTypeExtension extends AbstractTypeExtension
{
    private ObjectRepository $repository;

    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                $builder->create('place', HiddenType::class, [
                    'required' => false,
                    'attr' => ['class' => 'click_n_collect_place'],
                ])->addModelTransformer(new CallbackTransformer(function (?Place $place): string {
                    return $place ? (string) $place->getCode() : '';
                }, function (?string $code): ?Place {
                    if ('' === $code || null === $code) {
                        return null;
                    }

                    if (!$place = $this->repository->findOneBy(['code' => $code])) {
                        throw new TransformationFailedException(sprintf('Place "%s" doesn\'t exist.', $code));
                    }

                    return $place;
                }))
            )
            ->add(
                $builder->create('collectionTime', HiddenType::class, [
                    'required' => false,
                ])->addModelTransformer(new CallbackTransformer(function (?\DateTimeInterface $dateTime): string {
                    return $dateTime ? $dateTime->format(\DateTime::ATOM) : '';
                }, function (?string $value) {
                    if ('' === $value || null === $value) {
                        return null;
                    }

                    try {
                        // Dates are always sent as UTC because of browsers' limitations
                        // See https://fullcalendar.io/docs/timeZone#UTC-coercion
                        // Convert it in the local timezone for storage
                        return (new \DateTimeImmutable($value, new \DateTimeZone('UTC')))->setTimezone(new \DateTimeZone(date_default_timezone_get()));
                    } catch (\Exception $e) {
                        throw new TransformationFailedException('Invalid datetime format', 0, $e);
                    }
                }))
            );
    }

    public static function getExtendedTypes(): iterable
    {
        return [ShipmentType::class];
    }
}
