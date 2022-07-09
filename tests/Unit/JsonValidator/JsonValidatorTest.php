<?php

declare(strict_types=1);

namespace App\Tests\Unit\JsonValidator;

use App\JsonValidator\JsonValidationException;
use App\JsonValidator\JsonValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class JsonValidatorTest extends TestCase
{
    public function testValidateJsonRequestMissingSchema(): void
    {
        $schemaRoot = __DIR__ . '/../../data/';
        $validator = new JsonValidator($schemaRoot);
        $request = Request::create('/');

        $this->expectException(JsonValidationException::class);
        $this->expectExceptionMessage('Schema not found at ' . $schemaRoot . 'missing.json');
        $validator->validateJsonRequest($request, 'missing.json');
    }

    public function testValidateJsonRequestMalformedSentJson(): void
    {
        $schemaRoot = __DIR__ . '/../../data/';
        $validator = new JsonValidator($schemaRoot);
        $request = Request::create('/');

        $this->expectException(JsonValidationException::class);
        $this->expectExceptionMessage('Syntax error');
        $this->expectExceptionCode(JSON_ERROR_SYNTAX);
        $validator->validateJsonRequest($request, 'test-schema.json');
    }

    public function testValidateJsonMalformedSchema(): void
    {
        $schemaRoot = __DIR__ . '/../../data/';
        $validator = new JsonValidator($schemaRoot);
        $request = Request::create('/', 'POST', [], [], [], [], json_encode([]));

        $this->expectException(JsonValidationException::class);
        $this->expectExceptionMessage('JSON syntax is malformed');
        $this->expectExceptionCode(JSON_ERROR_SYNTAX);
        $validator->validateJsonRequest($request, 'malformed-schema.json');
    }

    public function testValidateJsonInvalid(): void
    {
        $schemaRoot = __DIR__ . '/../../data/';
        $validator = new JsonValidator($schemaRoot);
        $request = Request::create('/', 'POST', [], [], [], [], json_encode([]));

        $this->expectException(JsonValidationException::class);
        $this->expectExceptionMessage('Array value found, but an object is required');
        $this->expectExceptionCode(1);
        $validator->validateJsonRequest($request, 'test-schema.json');
    }

    public function testValidateJson(): void
    {
        $schemaRoot = __DIR__ . '/../../data/';
        $validator = new JsonValidator($schemaRoot);
        $request = Request::create('/', 'POST', [], [], [], [], json_encode([ 'test' => 'https://test.test' ]));

        $json = $validator->validateJsonRequest($request, 'test-schema.json');
        $this->assertIsObject($json);
        $this->assertObjectHasAttribute('test', $json);
        $this->assertSame('https://test.test', $json->test);
    }
}
