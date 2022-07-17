<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\LinkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(private readonly LinkRepository $linkRepository)
    {
    }

    #[Route('/', name: 'home', methods: [ 'GET' ])]
    public function home(): Response
    {
        $links = $this->linkRepository->fetchLinks(publicOnly: $this->getUser() === null);

        return $this->render('home.html.twig', [
            'links' => $links,
        ]);
    }
}
