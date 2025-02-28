<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/book')]
class BookController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private BookRepository $repository,
    ) {}

    #[Route('', name: 'app_admin_book_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $book = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($this->repository->createQueryBuilder('b')),
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('/admin/book/index.html.twig', [
            'books' => $book
        ]);
    }

    #[Route('/new', name: 'app_admin_book_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_book_edit', requirements: ["id" => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function new(?Book $book, Request $request): Response
    {
        $book ??= new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($book);
            $this->manager->flush();
            return $this->redirectToRoute('app_admin_book_show', ['id' => $book->getId()]);
        }

        return $this->render('admin/book/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_book_show', requirements: ["id" => Requirement::DIGITS], methods: ['GET'])]
    public function show(?Book $book): Response
    {
        return $this->render('admin/book/show.html.twig', [
            'book' => $book,
        ]);
    }
}
