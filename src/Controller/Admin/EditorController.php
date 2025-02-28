<?php

namespace App\Controller\Admin;

use App\Entity\Editor;
use App\Form\EditorType;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/editor')]
class EditorController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private EditorRepository $repository,
    ) {}

    #[Route('', name: 'app_admin_editor_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $editor = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($this->repository->createQueryBuilder('e')),
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('admin/editor/index.html.twig', [
            'editors' => $editor,
        ]);
    }

    #[Route('/new', name: 'app_admin_editor_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'app_admin_editor_edit', requirements: ["id" => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function new(?Editor $editor, Request $request): Response
    {
        $editor ??= new Editor();
        $form = $this->createForm(EditorType::class, $editor);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($editor);
            $this->manager->flush();
            return $this->redirectToRoute('app_admin_editor_show', ['id' => $editor->getId()]);
        }

        return $this->render('admin/editor/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_editor_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(?Editor $editor): Response
    {
        return $this->render('admin/editor/show.html.twig', [
            'editor' => $editor,
        ]);
    }
}
