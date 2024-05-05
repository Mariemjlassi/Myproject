<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategorieType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoriesController extends AbstractController
{
    #[Route('/admin/categories', name: 'app_categories')]
    public function index(CategoriesRepository $rep): Response
    {
        $categories=$rep->findAll();
        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }


    #[Route('/admin/categorie/create', name: 'app_categorie_create')]
    public function create(EntityManagerInterface $manager,Request $request): Response
    {
        $categorie=new Categories();
        //construction de l'objet form
        $form=$this->createForm(CategorieType::class,$categorie);
        //recuperation et traitement de donnes
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $manager->persist($categorie);
            $manager->flush();
            return $this->redirectToRoute('app_categories');
        }



        return $this->render('categories/create.html.twig', [
            'f'=>$form
        ]);
    }

    #[Route('/admin/categorie/update/{id}', name: 'app_categorie_update')]
    public function update(Categories $cat,EntityManagerInterface $manager,Request $request): Response
    {
        
        //construction de l'objet form
        $form=$this->createForm(CategorieType::class,$cat);
        //recuperation et traitement de donnes
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $formData = $form->getData();
            //dd($formData);
            $manager->persist($cat);
            $manager->flush();
            return $this->redirectToRoute('app_categories');
        }



        return $this->render('categories/update.html.twig', [
            'f'=>$form
        ]);
    }
}
