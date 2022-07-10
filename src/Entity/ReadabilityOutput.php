<?php

declare(strict_types=1);

namespace App\Entity;

use JMS\Serializer\Annotation as Serializer;

class ReadabilityOutput
{
    public string $title;
    public ?string $byline = null;
    public ?string $dir;
    public string $lang;
    public string $content;
    #[Serializer\SerializedName('textContent')]
    public string $textContent;
    public int $length;
    public string $excerpt;
    public ?string $siteName = null;
}
