<?php

declare(strict_types=1);

namespace App\Form\Model;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class LinkModel
{
    #[Serializer\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Url(protocols: [ 'http', 'https' ])]
    public string $url;
    /**
     * @var array<string>
     */
    #[Serializer\Type('array<string>')]
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Type('string'),
    ])]
    public array $categories = [];
    /**
     * @var array<string>
     */
    #[Serializer\Type('array<string>')]
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Type('string'),
    ])]
    public array $tags = [];
}
