<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/author')]
class AuthorController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AuthorRepository $repository,
    ) {}

    #[Route('', name: 'app_admin_author_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $dates = [];
        if ($request->query->has('start')) {
            $dates['start'] = $request->query->get('start');
        }

        if ($request->query->has('end')) {
            $dates['end'] = $request->query->get('end');
        }

        $authors = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($this->repository->findByDateOfBirth()),
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('admin/author/index.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/new', name: 'app_admin_author_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_author_edit', requirements: ["id" => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function new(?Author $author, Request $request): Response
    {
        $author ??= new Author();
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($author);
            $this->manager->flush();
            return $this->redirectToRoute('app_admin_author_show', ['id' => $author->getId()]);
        }

        return $this->render('admin/author/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_author_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Author $author): Response
    {
        return $this->render('admin/author/show.html.twig', [
            'author' => $author,
        ]);
    }
}
