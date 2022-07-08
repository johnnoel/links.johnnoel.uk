<?php

declare(strict_types=1);

namespace App\Tests\Feature\Controller;

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
}
