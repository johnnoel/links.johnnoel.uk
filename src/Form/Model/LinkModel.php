<?php

declare(strict_types=1);

namespace App\Form\Model;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class LinkModel
{
    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Url(message: 'URL must begin with http or https', protocols: [ 'http', 'https' ])]
    public ?string $url = null;
    public bool $isPublic;
    /**
     * @var array<string>
     */
    #[Serializer\Type('array<string>')]
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Type('string'),
    ])]
    public ?array $categories = null;
    /**
     * @var array<string>
     */
    #[Serializer\Type('array<string>')]
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Type('string'),
    ])]
    public ?array $tags = null;
}
