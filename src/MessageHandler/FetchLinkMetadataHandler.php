<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Link;
use App\Entity\LinkMetadata;
use App\Message\FetchLinkMetadata;
use App\Repository\LinkRepository;
use Fusonic\OpenGraph\Consumer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class FetchLinkMetadataHandler
{
    private Client $http;

    public function __construct(private readonly LinkRepository $linkRepository)
    {
        // devtodo logging?
        $this->http = new Client([
            'timeout' => 10,
            'read_timeout' => 10,
            'connect_timeout' => 10,
        ]);
    }

    public function __invoke(FetchLinkMetadata $message): void
    {
        $link = $message->getLink();
        $content = '';

        try {
            $content = $this->http->get($link->getUrl())->getBody()->getContents();
        } catch (ClientException $e) {
            // devtodo flag the link according to the response code?
            return;
        }

        // devtodo check the returned content type and don't bother for images etc.

        $metadata = $this->getMetadataFromOpenGraph($link, $content) ?? $this->getMetadataFromPage($link, $content);

        if ($metadata === null) {
            // could not get metadata from the page, maybe a PDF or some other file?
            return;
        }

        $link->attachMetadata($metadata);
        $this->linkRepository->update($link);
    }

    private function getMetadataFromOpenGraph(Link $link, string $content): ?LinkMetadata
    {
        $openGraph = new Consumer();
        $openGraphData = $openGraph->loadHtml($content);

        if ($openGraphData->title === null) {
            return null;
        }

        return new LinkMetadata($link, $openGraphData->title, $openGraphData->description);
    }

    private function getMetadataFromPage(Link $link, string $content): ?LinkMetadata
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($content);

        $title = null;
        $titleNode = $crawler->filter('title');
        if ($titleNode->count() > 0) {
            $title = $titleNode->text();
        }

        if ($title === null) {
            return null;
        }

        $description = null;
        $descriptionNode = $crawler->filter('meta[name="description"]');
        if ($descriptionNode->count() > 0) {
            $description = $descriptionNode->attr('content');
        }

        return new LinkMetadata($link, $title, $description);
    }
}
