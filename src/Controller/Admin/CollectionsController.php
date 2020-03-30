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

namespace CoopTilleuls\SyliusClickNCollectPlugin\Controller\Admin;

use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Displays upcoming collections in a timetable.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class CollectionsController
{
    private ObjectRepository $locationRepository;
    private Environment $twig;

    public function __construct(ObjectRepository $locationRepository, Environment $twig)
    {
        $this->locationRepository = $locationRepository;
        $this->twig = $twig;
    }

    public function __invoke(): Response
    {
        return new Response(
            $this->twig->render(
                '@CoopTilleulsSyliusClickNCollectPlugin/Admin/collections.html.twig',
                ['locations' => $this->locationRepository->findBy(['enabled' => true])]
            )
        );
    }
}
