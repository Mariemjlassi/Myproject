<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReclamationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReclamationController extends AbstractController
{
    #[Route('/profile/reclamation', name: 'app_reclamation')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation->setUser($this->getUser());
            $reclamation->setDate(new \DateTime());
            $entityManager->persist($reclamation);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');

        }

        return $this->render('reclamation/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/reclamation/delete/{id}', name: 'app_reclamation_delete')]
    public function delete(EntityManagerInterface $entityManager, Reclamation $reclamation): Response
    {
        $entityManager->remove($reclamation);
        $entityManager->flush();

        return $this->redirectToRoute('app_reclamation_list');
    }
    
    #[Route('/reclamation', name: 'app_reclamation_list')]
    public function list(ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findAll();

        return $this->render('reclamation/lister.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }
}
