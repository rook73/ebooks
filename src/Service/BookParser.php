<?php

namespace App\Service;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Service\BookFormat\BookFormatInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BookParser
{
    /** @var BookFormatInterface[] */
    private array $formats;

    private BookRepository $bookRepository;
    private AuthorRepository $authorRepository;

    public function __construct(BookRepository $bookRepository, AuthorRepository $authorRepository)
    {
        $this->formats = [];
        $this->bookRepository = $bookRepository;
        $this->authorRepository = $authorRepository;
    }

    public function addFormat(BookFormatInterface $format)
    {
        $this->formats[] = $format;
    }

    public function parseFile(UploadedFile $file): ?Book
    {
        foreach ($this->formats as $parser) {
            if ($parser->init($file)->valid()) {
                $author = $this->getAuthor($parser->getAuthor());

                return $this->getBook($author, $parser->getTitle(), $parser->getLang());
            }
        }

        return null;
    }

    private function getAuthor(string $name): Author
    {
        return $this->authorRepository->findOneByName($name) ?? (new Author())->setName($name);
    }

    private function getBook(Author $author, string $title, ?string $lang): Book
    {
        return $this->bookRepository->findOneBy(
                [
                    'author' => $author,
                    'title' => $title,
                    'lang' => $lang,
                ]
            ) ?? (new Book())->setAuthor($author)->setTitle($title)->setLang($lang);
    }
}