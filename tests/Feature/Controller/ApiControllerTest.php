<?php

declare(strict_types=1);

namespace App\Tests\Feature\Controller;

use App\Factory\CategoryFactory;
use App\Factory\LinkFactory;
use App\Factory\TagFactory;
use App\Factory\UserFactory;
use App\Tests\WebTestCase;
use Helmich\JsonAssert\JsonAssertions;
use Symfony\Component\HttpFoundation\Response;

class ApiControllerTest extends WebTestCase
{
    use JsonAssertions;

    public function testListLinksExists(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/1/links');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testListLinksListsLinks(): void
    {
        $client = static::createClient();
        $tags = TagFactory::createMany(3);
        $link = LinkFactory::createOne([ 'tags' => $tags ]);

        $client->request('GET', '/api/1/links');

        $this->assertResponseIsSuccessful();
        $json = $client->getResponse()->getContent();
        $this->assertJson($json);
        $this->assertJsonValueEquals($json, '$[0].url', $link->getUrl());
    }

    /**
     * @dataProvider createLinkProvider
     */
    public function testCreateLink(array $jsonData): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne();
        $client->loginUser($user->object());
        $client->request('POST', '/api/1/links', [], [], [], json_encode($jsonData));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function createLinkProvider(): array
    {
        return [
            'just link' => [ [ 'url' => 'https://links.johnnoel.uk' ] ],
            'link + tags' => [ [ 'url' => 'https://links.johnnoel.uk', 'tags' => [ 'a', 'b', 'c' ] ] ],
            'link + categories' => [ [ 'url' => 'https://links.johnnoel.uk', 'categories' => [ 'a', 'b', 'c' ] ] ],
            'link + categories + tags' => [ [
                'url' => 'https://links.johnnoel.uk',
                'categories' => [ 'a', 'b', 'c' ],
                'tags' => [ 'a', 'b', 'c' ],
            ] ],
        ];
    }

    /**
     * @dataProvider createLinkFailsProvider
     */
    public function testCreateLinkFails(array $jsonData, string $errorMessage): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne();
        $client->loginUser($user->object());
        $client->request('POST', '/api/1/links', [], [], [], json_encode($jsonData));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString($errorMessage, $client->getResponse()->getContent());
    }

    public function createLinkFailsProvider(): array
    {
        return [
            'nothing' => [ [], 'Array value found, but an object is required' ],
            'bad url' => [ [ 'url' => '"£$%this isnt a url!' ], 'This value is not a valid URL' ],
            'bad tags' => [ [ 'url' => 'https://links.johnnoel.uk', 'tags' => 123 ], 'Integer value found, but an array is required' ],
            'bad tags 2' => [ [ 'url' => 'https://links.johnnoel.uk', 'tags' => [ [ 'another array' ] ] ], 'Array value found, but a string is required' ],
            'bad categories' => [ [ 'url' => 'https://links.johnnoel.uk', 'categories' => 123 ], 'Integer value found, but an array is required' ],
            'bad categories 2' => [ [ 'url' => 'https://links.johnnoel.uk', 'categories' => [ [ 'another array' ] ] ], 'Array value found, but a string is required' ],
        ];
    }

    public function testListCategoriesExists(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/1/categories');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testListCategoriesListsCategories(): void
    {
        $client = static::createClient();
        $category = CategoryFactory::createOne();

        $client->request('GET', '/api/1/categories');

        $this->assertResponseIsSuccessful();
        $json = $client->getResponse()->getContent();
        $this->assertJson($json);
        $this->assertJsonValueEquals($json, '$[0].name', $category->getName());
    }

    /**
     * @dataProvider createCategoryProvider
     */
    public function testCreateCategory(array $jsonData): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne();
        $client->loginUser($user->object());
        $client->request('POST', '/api/1/categories', [], [], [], json_encode($jsonData));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJson($client->getResponse()->getContent());
    }

    public function createCategoryProvider(): array
    {
        return [
            'name' => [ [ 'name' => 'Test Category' ] ],
        ];
    }
}
