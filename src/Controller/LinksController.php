<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Model\LinkModel;
use App\Form\Type\LinkType;
use App\Message\CreateLink;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class LinksController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    #[Route('/links/create', methods: [ 'GET', 'POST' ])]
    public function create(Request $request): Response
    {
        $form = $this->createForm(LinkType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var LinkModel $formData */
            $formData = $form->getData();

            $this->handle(new CreateLink(strval($formData->url), $formData->categories ?? [], $formData->tags ?? []));
            $this->addFlash('success', 'Successfully added link');

            return $this->redirectToRoute('home');
        }

        return $this->render('link.html.twig', [
            'form' => $form->createView(),
            'form_type' => 'Create',
        ]);
    }

    public function update(Request $request): Response
    {

    }
}
