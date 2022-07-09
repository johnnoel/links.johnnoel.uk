<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Category;
use App\Message\CreateCategory;
use App\Repository\CategoryRepository;
use Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateCategoryHandler
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    public function __invoke(CreateCategory $message): Category
    {
        $slug = Category::createSlug($message->getName());
        $originalSlug = $slug;
        $counter = 1;

        while (count($this->categoryRepository->fetchCategoriesBySlug([ $slug ])) > 0) {
            $slug = $originalSlug . '-' . $counter;

            if ($counter++ >= 10) {
                throw new Exception('Category with slug ' . $originalSlug . ' already exists');
            }
        }

        $category = new Category($message->getName(), $slug);
        $this->categoryRepository->create($category);

        return $category;
    }
}
