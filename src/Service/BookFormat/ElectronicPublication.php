<?php

namespace App\Service\BookFormat;

use lywzx\epub\EpubParser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ElectronicPublication implements BookFormatInterface
{
    protected EpubParser $parser;

    public function init(UploadedFile $file): self
    {
        try {
            $this->parser = new EpubParser($file->getPathname());
        } catch (\Throwable $e) {
            return $this;
        }

        return $this;
    }

    public function valid(): bool
    {
        try {
            $this->parser->parse();
            return !empty($this->parser->getDcItem());
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public function getAuthor(): ?string
    {
        return $this->parser->getDcItem('creator');
    }

    public function getTitle(): ?string
    {
        return $this->parser->getDcItem('title');
    }

    public function getLang(): ?string
    {
        return $this->parser->getDcItem('language');
    }
}