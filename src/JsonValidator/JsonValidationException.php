<?php

declare(strict_types=1);

namespace App\JsonValidator;

use Exception;
use Throwable;

class JsonValidationException extends Exception
{
    /**
     * @param array<mixed> $errors
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        private array $errors = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array<mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
