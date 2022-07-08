<?php

declare(strict_types=1);

namespace App\Tests\Feature\Controller;

use App\Factory\CategoryFactory;
use App\Factory\LinkFactory;
use App\Tests\WebTestCase;
use Helmich\JsonAssert\JsonAssertions;

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
        $link = LinkFactory::createOne();

        $client->request('GET', '/api/1/links');

        $this->assertResponseIsSuccessful();
        $json = $client->getResponse()->getContent();
        $this->assertJson($json);
        $this->assertJsonValueEquals($json, '$[0].url', $link->getUrl());
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

        dump($client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $json = $client->getResponse()->getContent();
        $this->assertJson($json);
        $this->assertJsonValueEquals($json, '$[0].name', $category->getName());
    }
}
