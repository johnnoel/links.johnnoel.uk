<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\CreateUser;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
class CreateUserHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function __invoke(CreateUser $message): void
    {
        $user = new User($message->getEmail(), $message->getPassword());
        $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));

        $this->userRepository->create($user);
    }
}
