<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\ByteString;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;
    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: true)]
    private string $email;
    #[ORM\Column(type: 'string', length: 255)]
    private string $password;
    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $apiToken;

    public function __construct(
        string $email,
        string $password
    ) {
        $this->id = Uuid::uuid4()->toString();
        $this->email = $email;
        $this->password = $password;
        $this->apiToken = ByteString::fromRandom(64)->toString();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        return [ 'ROLE_USER' ];
    }

    public function eraseCredentials(): void
    {
        // N/A
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
