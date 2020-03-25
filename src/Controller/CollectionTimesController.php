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

namespace CoopTilleuls\SyliusClickNCollectPlugin\Controller;

use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CollectionTimesController
{
    private ObjectRepository $shipmentRepository;
    private ObjectRepository $placeRepository;
    /**
     * @var callable
     */
    private $availableSlotsComputer;

    public function __construct(ObjectRepository $shipmentRepository, ObjectRepository $placeRepository, callable $availableSlotsComputer)
    {
        $this->shipmentRepository = $shipmentRepository;
        $this->placeRepository = $placeRepository;
        $this->availableSlotsComputer = $availableSlotsComputer;
    }

    public function __invoke(Request $request, int $shipmentId, string $placeCode): JsonResponse
    {
        if (!$shipment = $this->shipmentRepository->find($shipmentId)) {
            throw new NotFoundHttpException(sprintf('The shipment "%d" doesn\'t exist.', $shipmentId));
        }

        if (!$place = $this->placeRepository->findOneBy(['code' => $placeCode])) {
            throw new NotFoundHttpException(sprintf('The place "%s" doesn\'t exist.', $placeCode));
        }

        $start = $request->query->get('start');
        $end = $request->query->get('end');

        try {
            $startDateTime = null === $start ? null : new \DateTimeImmutable($start);
            $endDateTime = null === $start ? null : new \DateTimeImmutable($end);

            $recurrences = ($this->availableSlotsComputer)($shipment, $place, $startDateTime, $endDateTime);
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Invalid date range (bad format, in the past or longer than 1 month)', $e);
        }

        $collectionTimes = [];
        foreach ($recurrences as $recurrence) {
            $collectionTimes[] = [
                'id' => $id = $recurrence->getStart()->format(\DateTime::ATOM),
                'start' => $id,
                'end' => $recurrence->getEnd()->format(\DateTime::ATOM),
            ];
        }

        return new JsonResponse($collectionTimes);
    }
}
