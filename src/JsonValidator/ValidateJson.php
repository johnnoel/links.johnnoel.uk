<?php

declare(strict_types=1);

namespace App\JsonValidator;

use Attribute;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"CLASS", "METHOD"})
 */
#[Attribute(Attribute::TARGET_METHOD)]
class ValidateJson extends ConfigurationAnnotation
{
    /**
     * @param array<string,mixed> $values
     * @param array<string> $methods
     */
    public function __construct(
        array $values = [],
        private ?string $path = null,
        private array $methods = []
    ) {
        $values['path'] ??= $path;
        $values['methods'] ??= $this->methods;

        $values = array_filter($values, function ($v) {
            return $v !== null;
        });

        parent::__construct($values);
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function getPath(): string|null
    {
        return $this->path;
    }

    /**
     * @param array<string> $methods
     */
    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }

    /**
     * @return array<string>
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getAliasName(): string
    {
        return 'validate_json';
    }

    public function allowArray(): bool
    {
        return false;
    }
}
