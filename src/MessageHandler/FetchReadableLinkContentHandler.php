<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\ReadabilityOutput;
use App\Message\FetchReadableLinkContent;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Process\Process;

#[AsMessageHandler]
class FetchReadableLinkContentHandler
{
    public function __construct(
        private readonly string $scriptPath,
        private readonly string $nodeExecutable,
        private readonly SerializerInterface $serializer
    ) {
    }

    public function __invoke(FetchReadableLinkContent $message): ReadabilityOutput
    {
        $link = $message->getLink();

        $process = new Process([
            $this->nodeExecutable,
            $this->scriptPath,
            $link->getUrl(),
        ]);
        $process->mustRun();

        $rawJson = $process->getOutput();

        /** @var ReadabilityOutput */
        return $this->serializer->deserialize($rawJson, ReadabilityOutput::class, 'json');
    }
}
