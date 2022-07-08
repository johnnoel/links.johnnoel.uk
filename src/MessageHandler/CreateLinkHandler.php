<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Link;
use App\Message\CreateLink;
use App\Repository\CategoryRepository;
use App\Repository\LinkRepository;
use App\Repository\TagRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateLinkHandler
{
    public function __construct(
        private readonly LinkRepository $linkRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly TagRepository $tagRepository,
    ) {
    }

    public function __invoke(CreateLink $message): Link
    {
        // 404 if categories can't be found?
        $categories = $this->categoryRepository->fetchCategoriesBySlug($message->getCategories());
        $tags = $this->tagRepository->fetchAndCreateTags($message->getTags());

        $link = new Link($message->getUrl(), $categories, $tags);
        $this->linkRepository->create($link);

        return $link;
    }
}
