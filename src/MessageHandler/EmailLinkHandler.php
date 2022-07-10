<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\ReadabilityOutput;
use App\Message\EmailLink;
use App\Message\FetchReadableLinkContent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class EmailLinkHandler
{
    use HandleTrait;

    public function __construct(
        private readonly MailerInterface $mailer,
        MessageBusInterface $messageBus
    ) {
        $this->messageBus = $messageBus;
    }

    public function __invoke(EmailLink $message): void
    {
        $link = $message->getLink();
        /** @var ReadabilityOutput $readability */
        $readability = $this->handle(new FetchReadableLinkContent($link));

        $email = (new Email())
            ->to($message->getEmailAddress())
            ->subject('Link content for ' . $link->getDomain())
            ->text(trim($readability->textContent))
            ->html(trim($readability->content))
        ;

        $this->mailer->send($email);
    }
}
