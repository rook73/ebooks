<?php

namespace App\Service\BookFormat;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface BookFormatInterface
{
    public function init(UploadedFile $file): self;

    public function valid(): bool;

    public function getAuthor(): ?string;

    public function getTitle(): ?string;

    public function getLang(): ?string;
}
