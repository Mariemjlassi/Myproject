<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LivresController extends AbstractController
{
    #[Route('/livres', name: 'app_livres')]
    public function index(LivresRepository $rep): Response
    {
        /*$livre=$rep->findGreaterThan(100);
        dd($livre);*/
        $livres=$rep->findAll();
        return $this->render('livres/index.html.twig', [
            'livres' => $livres,
        ]);
    }

    
    #[Route('/livres/show/{id}', name: 'app_livres_details')]
    public function Show(LivresRepository $rep,$id)
    {
       $livre=$rep->find($id);
       return $this->render('livres/show.html.twig',[
        'livre'=>$livre,
       ]);
    }

    #[Route('/livres/create', name: 'app_livres_create')]
    public function create(EntityManagerInterface $em)
    {
       $livre=new Livres();
       $livre->setEditeur('Eni')
            ->setDateEdition(new \DateTime('01-01-2023'))
            ->setTitre("Titre " .$livre->getId())
            ->setResume('resumee titre 1')
            ->setSlug('titre-1')
            ->setPrix(200)
            ->setImage('https://picsum.photos/300')
            ->setIsbn('111.1111.1111.1115');
            $em->persist($livre);
            $em->flush();
            dd($livre);
       
    }

    #[Route('/livres/delete/{id}', name: 'app_livres_delete')]
    public function delete(EntityManagerInterface $em,Livres $livre)
    {
       
       $em->remove($livre);
       $em->flush();
       return $this->redirectToRoute('app_livres');
       
       
    }
    
    //creer une methode update qui permet en connaissant id du livre de modifier son prix
    #[Route('/livres/update/{id}', name: 'app_livres_update')]
    public function update(EntityManagerInterface $em, Livres $livre): Response
{
    $nvPrix =50;
    $p=$livre->getPrix();
    $livre->setPrix($nvPrix+$p);
    $em->flush();
    
    return $this->redirectToRoute('app_livres'); 
}

}
