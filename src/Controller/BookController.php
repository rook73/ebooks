<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Form\BookUploadType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Service\BookParser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class BookController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render(
            'book/index.html.twig',
            [
                'books' => $bookRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/upload", name="book_upload", methods={"GET","POST"})
     */
    public function upload(Request $request, BookParser $bookParser): Response
    {
        $form = $this->createForm(BookUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('book')->getData();

            $book = $bookParser->parseFile($file);

            if (null !== $book) {

                $this->em->persist($book->getAuthor());
                $this->em->persist($book);
                $this->em->flush();

                return $this->redirectToRoute('book_index');
            } else {
                $form->addError(new FormError('Неверный файл.'));
            }
        }

        return $this->render(
            'book/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/new", name="book_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'book/new.html.twig',
            [
                'book' => $book,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="book_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Book $book): Response
    {
        return $this->render(
            'book/show.html.twig',
            [
                'book' => $book,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="book_edit", methods={"GET","POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'book/edit.html.twig',
            [
                'book' => $book,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="book_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, Book $book): Response
    {
        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('book_index');
    }

    /**
     * @Route("/count", name="book_count", methods={"GET"})
     */
    public function countBooks(AuthorRepository $authorRepository): Response
    {
        return $this->render(
            'book/count.html.twig',
            [
                'authors' => $authorRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/authors", name="authors_without_books", methods={"GET"})
     */
    public function authorsWithoutBooks(AuthorRepository $authorRepository): Response
    {
        return $this->render(
            'book/authors_without_books.html.twig',
            [
                'authors' => $authorRepository->findWithoutBooks(),
            ]
        );
    }

    /**
     * @Route("/by-date", name="books_by_date", methods={"GET"})
     */
    public function byDate(BookRepository $bookRepository): Response
    {
        return $this->render(
            'book/by_date.html.twig',
            [
                'dates' => $bookRepository->booksByDates(),
            ]
        );
    }
}
