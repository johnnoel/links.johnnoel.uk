<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Link;
use App\Message\EmailLink;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:email-link-contents',
    description: 'Add a short description for your command',
)]
class EmailLinkContentsCommand extends Command
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        parent::__construct();

        $this->messageBus = $messageBus;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'URL to email the contents')
            ->addArgument('email', InputArgument::REQUIRED, 'Email address')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $url = strval($input->getArgument('url'));
        $email = strval($input->getArgument('email'));

        $this->handle(new EmailLink(new Link($url), $email));

        $io->success('The contents of URL ' . $url . ' have been sent to ' . $email);

        return Command::SUCCESS;
    }
}
