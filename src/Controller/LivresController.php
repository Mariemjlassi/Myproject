<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Form\CategorieType;
use App\Form\LivresType;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LivresController extends AbstractController
{
    #[Route('/admin/livres', name: 'app_livres')]
    
    public function index(LivresRepository $rep): Response
    {
        /*$livre=$rep->findGreaterThan(100);
        dd($livre);*/
        $livres=$rep->findAll();
        return $this->render('livres/index.html.twig', [
            'livres' => $livres,
        ]);
    }

    #[Route('/admin/livre/update/{id}', name: 'app_Liv_update')]
    public function updateLivre(Livres $livre,EntityManagerInterface $manager,Request $request): Response
    {
        
        //construction de l'objet form
        $form=$this->createForm(LivresType::class,$livre);
        //recuperation et traitement de donnes
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $formData = $form->getData();
            //dd($formData);
            $manager->persist($livre);
            $manager->flush();
            return $this->redirectToRoute('app_livres');
        }



        return $this->render('categories/update.html.twig', [
            'f'=>$form
        ]);
    }

    
    #[Route('/admin/livres/show/{id}', name: 'app_livres_details')]
    public function Show(LivresRepository $rep,$id)
    {
       $livre=$rep->find($id);
       return $this->render('livres/show.html.twig',[
        'livre'=>$livre,
       ]);
    }

    

    #[Route('/admin/livres/delete/{id}', name: 'app_livres_delete')]
    public function delete(EntityManagerInterface $em,Livres $livre)
    {
       
       $em->remove($livre);
       $em->flush();
       return $this->redirectToRoute('app_livres');
       
       
    }
    
    //creer une methode update qui permet en connaissant id du livre de modifier son prix
    #[Route('/admin/livres/update/{id}', name: 'app_livres_modify')]
    public function update(EntityManagerInterface $em, Livres $livre): Response
{
    $nvPrix =50;
    $p=$livre->getPrix();
    $livre->setPrix($nvPrix+$p);
    $em->flush();
    
    return $this->redirectToRoute('app_livres'); 
}
#[Route('/admin/livre/add', name: 'app_livre_add')]
    public function addLivre(EntityManagerInterface $manager,Request $request): Response
    {
        $livre=new Livres();
        //construction de l'objet form
        $form=$this->createForm(LivresType::class,$livre);
        //recuperation et traitement de donnes
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $manager->persist($livre);
            $manager->flush();
            return $this->redirectToRoute('app_livres');
        }



        return $this->render('livres/add.html.twig', [
            'f'=>$form
        ]);
    }

    #[Route('/admin/livres/create', name: 'app_livres_create')]
    public function create(EntityManagerInterface $em)
    {
       $livre=new Livres();
       $livre->setEditeur('Eni')
            ->setDateEdition(new \DateTime('01-01-2023'))
            ->setTitre("Titre " .$livre->getId())
            ->setResume('resumee titre 1')
            ->setSlug('titre-1')
            ->setPrix(200)
            ->setQte(10)
            ->setImage('https://picsum.photos/300')
            ->setIsbn('111.1111.1111.1115');
            $em->persist($livre);
            $em->flush();
            dd($livre);
       
    }
    

}
