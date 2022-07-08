<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\LinkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/1/', name: 'api_')]
class ApiController extends AbstractController
{
    public function __construct(private readonly LinkRepository $linkRepository)
    {
    }

    #[Route('links', name: 'links')]
    public function listLinks(): Response
    {
        $links = $this->linkRepository->fetchLinks();

        return new JsonResponse($links);
    }
}
