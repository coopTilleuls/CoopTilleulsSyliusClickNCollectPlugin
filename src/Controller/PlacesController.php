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

namespace CoopTilleuls\SyliusClickNCollectPlugin\Controller;

use CoopTilleuls\SyliusClickNCollectPlugin\Entity\Place;
use Doctrine\Persistence\ObjectRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns the list of Click and Collect places associated with a given shipping method.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class PlacesController
{
    private ObjectRepository $repository;
    private SerializerInterface $serializer;

    public function __construct(ObjectRepository $repository, SerializerInterface $serializer)
    {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    public function __invoke(string $shippingMethodCode): JsonResponse
    {
        /*
         * @var ClickNCollectShippingMethodeInterface|null
         */
        if (!$shippingMethod = $this->repository->findOneBy(['code' => $shippingMethodCode])) {
            throw new NotFoundHttpException(sprintf('The shipping method "%s" doesn\'t exist.', $shippingMethodCode));
        }

        // TODO: do this at the SQL layer in a repository, at the same do a proper JOIN...
        $places = $shippingMethod->getPlaces()->filter(function (Place $p) {
            return $p->isEnabled();
        });

        // TODO: add groups to filter unneeded props
        return new JsonResponse($this->serializer->serialize($places, 'json'), 200, [], true);
    }
}
