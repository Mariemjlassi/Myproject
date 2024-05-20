<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\LivresRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_dashboard')]
    public function index(CommandeRepository $commandeRepository,LivresRepository $LivresRepository): Response
    {
        $totalCommandes = count($commandeRepository->findAll());
        $livreLePlusVendu = $LivresRepository->findMostSoldBook();

        $livres= $LivresRepository->findAll();
        $donneesGraphique = [['Livre', 'Nombre de ventes']];

        foreach ($livres as $livre) {
            $donneesGraphique[] = [$livre->getTitre(), $livre->getNombreDeVentes()];
        }

        return $this->render('dashboard/index.html.twig', [
            'totalCommandes' => $totalCommandes,
            'livreLePlusVendu' => $livreLePlusVendu,
            'donneesGraphique' => $donneesGraphique,
        ]);
    }
}
