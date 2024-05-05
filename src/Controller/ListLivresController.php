<?php

namespace App\Controller;

use App\Repository\LivresRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListLivresController extends AbstractController
{
    #[Route('/profile/listlivres', name: 'app_list_livres')]
    public function lister(LivresRepository $rep): Response
    {

        $livres=$rep->findAll();
        return $this->render('list_livres/index.html.twig', [
            'livres' => $livres,
        ]);
    }


    #[Route('/profile/livre/show/{id}', name: 'app_livre_details')]
    public function details(LivresRepository $rep,$id)
    {
       $livre=$rep->find($id);
       return $this->render('list_livres/detailsLivre.html.twig',[
        'livre'=>$livre,
       ]);
    }
}
