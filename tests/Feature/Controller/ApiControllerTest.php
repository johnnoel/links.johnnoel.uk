<?php

declare(strict_types=1);

namespace App\Tests\Feature\Controller;

use App\Factory\LinkFactory;
use App\Tests\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    public function testListLinksExists(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/1/links');

        $this->assertResponseIsSuccessful();
        $this->assertJson('[]');
    }

    public function testListLinksListsLinks(): void
    {
        $client = static::createClient();
        $link = LinkFactory::createOne();

        $client->request('GET', '/api/1/links');

        $this->assertResponseIsSuccessful();
        $this->assertJson('[]'); // no output yet
    }
}
