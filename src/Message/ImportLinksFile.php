<?php

declare(strict_types=1);

namespace App\Message;

class ImportLinksFile
{
    public function __construct(private readonly string $filePath)
    {
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
