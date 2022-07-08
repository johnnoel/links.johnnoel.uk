<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\LinkRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/1/', name: 'api_')]
class ApiController extends AbstractController
{
    public function __construct(
        private readonly LinkRepository $linkRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly SerializerInterface $serializer
    ) {
    }

    #[Route('links', name: 'links')]
    public function listLinks(): Response
    {
        $links = $this->linkRepository->fetchLinks();
        $serialized = $this->serializer->serialize($links, 'json');

        return new JsonResponse($serialized, Response::HTTP_OK, [], true);
    }

    #[Route('categories', name: 'categories')]
    public function listCategories(): Response
    {
        $categories = $this->categoryRepository->fetchCategories();
        $serialized = $this->serializer->serialize($categories, 'json');

        return new JsonResponse($serialized, Response::HTTP_OK, [], true);
    }
}
