<?php

namespace App\Controller;

use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AvisController extends AbstractController
{
    #[Route('/avis', name: 'app_avis')]
    public function index(): Response
    {
        return $this->render('avis/index.html.twig', [
            'controller_name' => 'AvisController',
        ]);
    }

    
    #[Route('/admin/list/avis', name: 'app_list_avis')]
    public function listAvis(AvisRepository $avisRep): Response
    {
        $avis = $avisRep->findAll();
        return $this->render('avis/index.html.twig', [
            'avis' => $avis,
        ]);
    }

}