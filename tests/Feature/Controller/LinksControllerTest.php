<?php

declare(strict_types=1);

namespace App\Tests\Feature\Controller;

use App\Factory\UserFactory;
use App\Tests\WebTestCase;

class LinksControllerTest extends WebTestCase
{
    public function testCreateRequiresAuthentication(): void
    {
        $client = static::createClient();

        $client->request('GET', '/links/create');
        $this->assertResponseRedirects('http://localhost/login');

        $user = UserFactory::createOne();
        $client->loginUser($user->object());

        $client->request('GET', '/links/create');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider createProvider
     */
    public function testCreate(array $postData): void
    {
        $client = static::createClient();
        $user = UserFactory::createOne();
        $client->loginUser($user->object());

        $postData = array_combine(
            array_map(fn (string $k): string => 'link[' . $k . ']', array_keys($postData)),
            array_values($postData)
        );

        $client->request('GET', '/links/create');
        $client->submitForm('Create link', $postData);
        $this->assertResponseRedirects('/');
    }

    public function createProvider(): array
    {
        return [
            'just link' => [ [ 'url' => 'https://links.johnnoel.uk' ] ],
            'link + tags' => [ [ 'url' => 'https://links.johnnoel.uk', 'tags' => 'a, b, c' ] ],
            'link + categories' => [ [ 'url' => 'https://links.johnnoel.uk', 'categories' => 'a, b, c' ] ],
            'link + categories + tags' => [ [
                'url' => 'https://links.johnnoel.uk',
                'categories' => 'a, b, c',
                'tags' => 'a, b, c',
            ] ],
        ];
    }
}
