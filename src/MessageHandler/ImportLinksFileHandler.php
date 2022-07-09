<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Category;
use App\Entity\Link;
use App\Message\ImportLinksFile;
use App\Repository\CategoryRepository;
use App\Repository\LinkRepository;
use App\Repository\TagRepository;
use DateTimeImmutable;
use DateTimeInterface;
use League\Csv\Reader;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ImportLinksFileHandler
{
    /**
     * @var array<string,Category>
     */
    private array $categoryCache = [];

    public function __construct(
        private readonly LinkRepository $linkRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly TagRepository $tagRepository
    ) {
    }

    public function __invoke(ImportLinksFile $message): int
    {
        $path = $message->getFilePath();
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        $imported = 0;

        /** @var array{url: string, folder: string, tags: string, created: string} $record */
        foreach ($csv->getRecords() as $record) {
            $url = $record['url'];
            $tags = array_filter(array_map('trim', explode(',', $record['tags'])));
            $tags = (count($tags) > 0) ? $this->tagRepository->fetchAndCreateTags($tags) : [];
            $created = DateTimeImmutable::createFromFormat(DateTimeInterface::RFC3339_EXTENDED, $record['created']);

            $link = new Link($url, [ $this->getCategory($record['folder']) ], $tags);
            if ($created !== false) {
                $link->overrideCreated($created);
            }

            $this->linkRepository->create($link);
            $imported++;
        }

        return $imported;
    }

    private function getCategory(string $name): Category
    {
        // already seen
        if (array_key_exists($name, $this->categoryCache)) {
            return $this->categoryCache[$name];
        }

        // check for existing
        $slug = Category::createSlug($name);
        $existingCategories = $this->categoryRepository->fetchCategoriesBySlug([ $slug ]);

        if (count($existingCategories) === 1) {
            $this->categoryCache[$name] = $existingCategories[0];

            return $this->categoryCache[$name];
        }

        // create new
        $category = new Category($name, $slug);
        $this->categoryRepository->create($category);
        $this->categoryCache[$name] = $category;

        return $category;
    }
}
