<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\LinkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(
        private readonly LinkRepository $linkRepository,
        private readonly CategoryRepository $categoryRepository
    ) {
    }

    #[Route('/', name: 'home', methods: [ 'GET' ])]
    public function home(): Response
    {
        $hasUser = $this->getUser() === null;
        $categories = $this->categoryRepository->fetchCategories(publicOnly: $hasUser);
        $links = $this->linkRepository->fetchLinks(publicOnly: $hasUser);

        return $this->render('home.html.twig', [
            'categories' => $categories,
            'links' => $links,
        ]);
    }
}
