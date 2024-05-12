<?php

namespace App\Controller;

use App\Repository\LivresRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListLivresController extends AbstractController
{
    #[Route('/listlivres', name: 'app_list_livres')]
    public function lister(LivresRepository $rep, PaginatorInterface $paginator, Request $request): Response
    {
        $livres = $rep->findAll();

        $pagination = $paginator->paginate(
            $livres,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('list_livres/index.html.twig', [
            'pagination' => $pagination,
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
