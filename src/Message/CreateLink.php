<?php

declare(strict_types=1);

namespace App\Message;

class CreateLink
{
    /**
     * @param array<string> $categories
     * @param array<string> $tags
     */
    public function __construct(
        private readonly string $url,
        private readonly array $categories,
        private readonly array $tags
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return array<string>
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @return array<string>
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}
