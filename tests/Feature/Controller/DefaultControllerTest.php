<?php

declare(strict_types=1);

namespace App\Tests\Feature\Controller;

use App\Factory\LinkFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testHomeExists(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    public function testHomeListsPublicLinks(): void
    {
        $client = static::createClient();
        $publicLink = LinkFactory::createOne([ 'isPublic' => true ]);

        $client->request('GET', '/');
        $this->assertStringContainsString($publicLink->getUrl(), $client->getResponse()->getContent());
    }

    public function testHomeOnlyListsPrivateLinksWhenLoggedIn(): void
    {
        $client = static::createClient();
        $privateLink = LinkFactory::createOne([ 'isPublic' => false ]);

        $client->request('GET', '/');
        $this->assertStringNotContainsString($privateLink->getUrl(), $client->getResponse()->getContent());

        $user = UserFactory::createOne();
        $client->loginUser($user->object());

        $client->request('GET', '/');
        $this->assertStringContainsString($privateLink->getUrl(), $client->getResponse()->getContent());
    }
}
