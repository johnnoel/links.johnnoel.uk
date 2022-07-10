<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\Link;

class FetchLinkMetadata
{
    public function __construct(private readonly Link $link)
    {
    }

    public function getLink(): Link
    {
        return $this->link;
    }
}
