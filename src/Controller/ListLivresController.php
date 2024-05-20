<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use App\Repository\LivresRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListLivresController extends AbstractController
{
    #[Route('/listlivres', name: 'app_list_livres')]
    public function lister(LivresRepository $rep, PaginatorInterface $paginator, Request $request,CategoriesRepository $cat): Response
    {
        $categories=$cat->findAll(); 
        $livres = $rep->findAll();

        $pagination = $paginator->paginate(
            $livres,
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('list_livres/index.html.twig', [
            'pagination' => $pagination,
            'categories' => $categories
        ]);
    }

    #[Route('/livre/show/{id}', name: 'app_livre_details')]
    public function details(LivresRepository $rep,$id,AvisRepository $avisRep)
    {
       $livre=$rep->find($id);
       $avis=$avisRep->findBy(['livre'=>$livre]);
       return $this->render('list_livres/detailsLivre.html.twig',[
        'livre'=>$livre,
        'avis'=>$avis
       ]);
    }

    #[Route('/livre/avis/{id}', name: 'app_livre_avis')]
    public function addAvis(LivresRepository $rep,$id,EntityManagerInterface $em,Request $req)
    {
       $livre=$rep->find($id);
       $msg=$req->get('avis');
       $avis=new Avis();
      
       $user=$this->getUser();
       $avis->setMessage($msg);
$avis->setUser($user);
$avis->setLivre($livre);
       
        $livre->addAvi($avis);
           $em->persist($livre);
           $em->flush();
           return $this->redirectToRoute('app_livre_details', ['id' => $livre->getId()]);
       
       
       
     
    }



    #[Route('/livre/categorie/{id}', name: 'app_livre_categorie')]
    public function listParCategories(LivresRepository $lrep,int $id,CategoriesRepository $cate,Request $request,PaginatorInterface $paginator)
    {
      $categories=$cate->findAll();
      $cat=$cate->find($id);
      
      $livres=$lrep->findBy(['categorie'=>$cat]);
      $pagination = $paginator->paginate(
        $livres,
        $request->query->getInt('page', 1),
        3
    );
      return $this->render('list_livres/index.html.twig',[
        'livres'=>$livres,
        'categories'=>$categories,
        'pagination' => $pagination,
      ]);
    }

    #[Route('/livre/titre', name: 'app_livre_titre')]
public function listParTitre(Request $request, LivresRepository $livrep, CategoriesRepository $catrep,PaginatorInterface $paginator): Response
{
    $categories = $catrep->findAll();
    $searchTerm = $request->query->get('search');

    if ($searchTerm) {
        $query = $livrep->createQueryBuilder('l')
            ->where('l.titre LIKE :titre')
            ->setParameter('titre', '%' . $searchTerm . '%')
            ->getQuery();

        $livres = $query->getResult();

    } else {
        
        $livres = $livrep->findAll();
    }
$pagination = $paginator->paginate(
        $livres,
        $request->query->getInt('page', 1),
        3
    );
    return $this->render('list_livres/index.html.twig', [
        'livres' => $livres,
        'categories' => $categories,
        'pagination' => $pagination,
    ]);

    
        
    }


    #[Route('/livre/filtrer', name: 'app_livre_filtrer')]
    public function filtrer(Request $request, LivresRepository $livresRepository, PaginatorInterface $paginator,CategoriesRepository $catrep): Response
    {
        
        $filtreTitre = $request->query->get('filtre_titre');
        $filtrePrixMin = $request->query->get('filtre_prix_min');
        $filtrePrixMax = $request->query->get('filtre_prix_max');
        $filtreEditeur = $request->query->get('filtre_editeur');
       
        $livres = $livresRepository->findByCriteria($filtreTitre, $filtrePrixMin, $filtrePrixMax, $filtreEditeur);
        $categories = $catrep->findAll();
        
        $pagination = $paginator->paginate(
            $livres,
            $request->query->getInt('page', 1),
            3
        );
    
        
        return $this->render('list_livres/index.html.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
          
        ]);
    }
}
