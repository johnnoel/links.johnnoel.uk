<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Link;
use App\Message\CreateLink;
use App\Message\FetchLinkMetadata;
use App\Repository\CategoryRepository;
use App\Repository\LinkRepository;
use App\Repository\TagRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class CreateLinkHandler
{
    use HandleTrait;

    public function __construct(
        private readonly LinkRepository $linkRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly TagRepository $tagRepository,
        MessageBusInterface $messageBus
    ) {
    }

    public function __invoke(CreateLink $message): Link
    {
        // 404 if categories can't be found?
        $categories = $this->categoryRepository->fetchCategoriesBySlug($message->getCategories());
        $tags = $this->tagRepository->fetchAndCreateTags($message->getTags());

        $link = new Link($message->getUrl(), $categories, $tags);
        $this->linkRepository->create($link);

        $this->handle(new FetchLinkMetadata($link));

        return $link;
    }
}
