<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\Link;

class EmailLink
{
    public function __construct(private readonly Link $link, private readonly string $emailAddress)
    {
    }

    public function getLink(): Link
    {
        return $this->link;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }
}
