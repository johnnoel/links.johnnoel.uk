<?php

declare(strict_types=1);

namespace App\JsonValidator;

use JsonSchema\Exception\JsonDecodingException;
use JsonSchema\Validator;
use Symfony\Component\HttpFoundation\Request;

class JsonValidator
{
    public function __construct(private readonly string $schemaRoot)
    {
    }

    public function validateJsonRequest(Request $request, string $schemaPath): object
    {
        $schema = realpath($this->schemaRoot . $schemaPath);

        if ($schema === false) {
            throw new JsonValidationException('Schema not found at ' . $this->schemaRoot . $schemaPath);
        }

        /** @var object|null $jsonObject */
        $jsonObject = json_decode($request->getContent());

        if ($jsonObject === null || json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonValidationException(json_last_error_msg(), json_last_error());
        }

        $validator = new Validator();

        try {
            $validator->check($jsonObject, (object)[ '$ref' => 'file://' . $schema ]);
        } catch (JsonDecodingException $e) {
            throw new JsonValidationException($e->getMessage(), $e->getCode(), [], $e);
        }

        if (!$validator->isValid()) {
            $errors = $validator->getErrors();

            throw new JsonValidationException($errors[0]['message'], $errors[0]['context'], $errors);
        }

        return $jsonObject;
    }
}
