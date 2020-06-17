<?php

namespace App\Service\BookFormat;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FictionBook implements BookFormatInterface
{
    protected Crawler $crawler;

    public function init(UploadedFile $file): self
    {
        $body = $file->openFile('r')->fread($file->getSize());
        $body = str_replace('xmlns:l', 'xmlnsl', $body);

        $this->crawler = new Crawler($body);

        return $this;
    }

    public function valid(): bool
    {
        try {
            return 'FictionBook' == $this->crawler->filterXPath('//FictionBook')->nodeName();
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public function getAuthor(): ?string
    {
        $author = $this->crawler->filterXPath('//FictionBook/description/title-info/author');

        return implode(
            ' ',
            [
                $author->filter('first-name')->text(),
                $author->filter('middle-name')->text(),
                $author->filter('last-name')->text(),
            ]
        );
    }

    public function getTitle(): ?string
    {
        return $this->crawler->filterXPath('//FictionBook/description/title-info/book-title')->text();
    }

    public function getLang(): ?string
    {
        return $this->crawler->filterXPath('//FictionBook/description/title-info/lang')->text();
    }
}