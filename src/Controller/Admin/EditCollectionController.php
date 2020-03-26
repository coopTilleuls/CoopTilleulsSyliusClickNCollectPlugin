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

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\SelectShippingType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Edits a collection.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class EditCollectionController
{
    private ObjectRepository $orderRepository;
    private FormFactoryInterface $formFactory;
    private ObjectManager $orderManager;
    private RouterInterface $router;
    private SessionInterface $session;
    private TranslatorInterface $translator;
    private Environment $twig;

    public function __construct(ObjectRepository $orderRepository, FormFactoryInterface $formFactory, ObjectManager $orderManager, RouterInterface $router, SessionInterface $session, TranslatorInterface $translator, Environment $twig)
    {
        $this->orderRepository = $orderRepository;
        $this->formFactory = $formFactory;
        $this->orderManager = $orderManager;
        $this->router = $router;
        $this->session = $session;
        $this->translator = $translator;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, string $orderId): Response
    {
        if (null === $order = $this->orderRepository->find($orderId)) {
            throw new NotFoundHttpException('Order %s not found', $orderId);
        }

        $form = $this->formFactory->create(SelectShippingType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order = $form->getData();
            $this->orderManager->flush();

            $this->session->getFlashBag()->add('success', $this->translator->trans('sylius.ui.success'));

            return new RedirectResponse($this->router->generate('sylius_admin_order_show', ['id' => $order->getId()]));
        }

        return new Response($this->twig->render(
            '@CoopTilleulsSyliusClickNCollectPlugin/Admin/edit_collection.html.twig',
            [
                'order' => $order,
                'form' => $form->createView(),
            ]
        ));
    }
}
