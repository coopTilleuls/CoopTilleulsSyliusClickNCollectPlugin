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

use CoopTilleuls\SyliusClickNCollectPlugin\CollectionTime\AvailableSlotsComputerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns the list of available time slots as Full Calendar events.
 *
 * @see https://fullcalendar.io/docs/event-object
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class CollectionTimesController
{
    private ObjectRepository $shipmentRepository;
    private ObjectRepository $locationRepository;
    private AvailableSlotsComputerInterface $availableSlotsComputer;

    public function __construct(ObjectRepository $shipmentRepository, ObjectRepository $locationRepository, AvailableSlotsComputerInterface $availableSlotsComputer)
    {
        $this->shipmentRepository = $shipmentRepository;
        $this->locationRepository = $locationRepository;
        $this->availableSlotsComputer = $availableSlotsComputer;
    }

    public function __invoke(Request $request, int $shipmentId, string $locationCode): JsonResponse
    {
        if (!$shipment = $this->shipmentRepository->find($shipmentId)) {
            throw new NotFoundHttpException(sprintf('The shipment "%d" doesn\'t exist.', $shipmentId));
        }

        if (!$location = $this->locationRepository->findOneBy(['code' => $locationCode])) {
            throw new NotFoundHttpException(sprintf('The location "%s" doesn\'t exist.', $locationCode));
        }

        $start = $request->query->get('start');
        $end = $request->query->get('end');

        try {
            $startDateTime = null === $start ? null : new \DateTimeImmutable($start);
            $endDateTime = null === $start ? null : new \DateTimeImmutable($end);

            $recurrences = ($this->availableSlotsComputer)($shipment, $location, $startDateTime, $endDateTime);
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Invalid date range (bad format, in the past or longer than 1 month)', $e);
        }

        $events = [];
        foreach ($recurrences as $recurrence) {
            $events[] = [
                'id' => $id = $recurrence->getStart()->format(\DateTime::ATOM),
                'start' => $id,
                'end' => $recurrence->getEnd()->format(\DateTime::ATOM),
            ];
        }

        return new JsonResponse($events);
    }
}
