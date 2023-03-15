<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Form\PublicationType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PublicationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;

#[Route('/publication')]
class PublicationController extends AbstractController
{

    protected PublicationRepository $repository;

    protected EntityManagerInterface $manager;

    public function __construct(PublicationRepository $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    #[Route('/', name: 'publication_listing')]
    public function index(): Response
    {
        // Récupérer les informations de l'utilisateur connecté
        // $user = $this->getUser();
        // dd($user);

        // dans le repository nous récupérons toutes les données
        $publlications = $this->repository->findAll();

        // dd($publlications);

        return $this->render('publication/index.html.twig', [
            'publications' => $publlications,
        ]);
    }

    #[Route('/create', name: 'publication_create')]
    #[Route('/update/{id}', name: 'publication_update', requirements: ['id' => '\d+'])]
    public function createPublication(Request $request, Publication $publication = null, SluggerInterface $slugger, FileUploader $fileUploader): Response
    {

        if (!$publication) {
            $publication = new Publication();
            $publication->setCreatedAt(new \DateTimeImmutable());
        } else {
            $publication->setUpdatedAt(new \DateTimeImmutable());
        }

        $form = $this->createForm(PublicationType::class, $publication, []);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $photoName = $fileUploader->upload($photoFile);
                $publication->setPhoto($photoName);
            }

            $this->manager->persist($publication);
            $this->manager->flush();

            return $this->redirectToRoute('publication_listing');
        }

        return $this->render('publication/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', 'publication_delete', requirements: ['id' => '\d+'])]
    public function deletePublication(Publication $publication): Response
    {
        $this->repository->remove($publication, true);

        return $this->redirectToRoute('publication_listing');
    }
}
