<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Link;
use App\Form\Model\LinkModel;
use App\Message\CreateCategory;
use App\Message\CreateLink;
use App\Repository\CategoryRepository;
use App\Repository\LinkRepository;
use JMS\Serializer\Exception\Exception;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/1/', name: 'api_', defaults: [ '_format' => 'json' ])]
class ApiController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        private readonly LinkRepository $linkRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly SerializerInterface $serializer,
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
    }

    #[Route('links', name: 'links', methods: [ 'GET' ])]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Lists all links',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Link::class))
        )
    )]
    #[OA\Tag('links')]
    public function listLinks(): Response
    {
        $links = $this->linkRepository->fetchLinks();
        $serialized = $this->serializer->serialize($links, 'json');

        return new JsonResponse($serialized, Response::HTTP_OK, [], true);
    }

    #[Route('links', name: 'links_create', methods: [ 'POST' ])]
    //#[OA\RequestBody(new Model(type: LinkModel::class))]
    #[OA\Tag('links')]
    #[Security(name: 'HttpBasic')]
    public function createLink(Request $request, ValidatorInterface $validator): Response
    {
        try {
            /** @var LinkModel $linkModel */
            $linkModel = $this->serializer->deserialize($request->getContent(), LinkModel::class, 'json');
        } catch (Exception $e) {
            $serialized = $this->serializer->serialize($e, 'json');

            return new JsonResponse($serialized, Response::HTTP_BAD_REQUEST, [], true);
        }

        $errors = $validator->validate($linkModel);

        if (count($errors) > 0) {
            $serialized = $this->serializer->serialize($errors, 'json');

            return new JsonResponse($serialized, Response::HTTP_BAD_REQUEST, [], true);
        }

        $link = $this->handle(new CreateLink($linkModel->url, $linkModel->categories, $linkModel->tags));
        $serialized = $this->serializer->serialize($link, 'json');

        return new JsonResponse($serialized, Response::HTTP_CREATED, [], true);
    }

    #[Route('categories', name: 'categories', methods: [ 'GET' ])]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Lists all categories',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Category::class))
        )
    )]
    #[OA\Tag('categories')]
    public function listCategories(): Response
    {
        $categories = $this->categoryRepository->fetchCategories();
        $serialized = $this->serializer->serialize($categories, 'json');

        return new JsonResponse($serialized, Response::HTTP_OK, [], true);
    }

    #[Route('categories', name: 'categories_create', methods: [ 'POST' ])]
    #[OA\Tag('categories')]
    public function createCategory(Request $request): Response
    {
        $category = $this->handle(new CreateCategory('Doot'));
        $serialized = $this->serializer->serialize($category, 'json');

        return new JsonResponse($serialized, Response::HTTP_CREATED, [], true);
    }
}
