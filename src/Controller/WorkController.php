<?php

namespace App\Controller;

use App\Entity\Work;
use App\Form\WorkType;
use App\Repository\WorkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/work')]
class WorkController extends AbstractController
{
    #[Route('/', name: 'app_work_index', methods: ['GET'])]
    public function index(WorkRepository $workRepository): Response
    {
        return $this->render('work/index.html.twig', [
            'works' => $workRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_work_new', methods: ['GET', 'POST'])]
    public function new(Request $request, WorkRepository $workRepository): Response
    {
        $work = new Work();
        $form = $this->createForm(WorkType::class, $work);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workRepository->save($work, true);

            return $this->redirectToRoute('app_work_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('work/new.html.twig', [
            'work' => $work,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_work_show', methods: ['GET'])]
    public function show(Work $work): Response
    {
        return $this->render('work/show.html.twig', [
            'work' => $work,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_work_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Work $work, WorkRepository $workRepository): Response
    {
        $form = $this->createForm(WorkType::class, $work);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workRepository->save($work, true);

            return $this->redirectToRoute('app_work_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('work/edit.html.twig', [
            'work' => $work,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_work_delete', methods: ['POST'])]
    public function delete(Request $request, Work $work, WorkRepository $workRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$work->getId(), $request->request->get('_token'))) {
            $workRepository->remove($work, true);
        }

        return $this->redirectToRoute('app_work_index', [], Response::HTTP_SEE_OTHER);
    }
}
