<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\PanierRepository;

use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    #[Route('/profile/commande/passer/{total}', name: 'passer_commande')]
    public function passerCommande(PanierRepository $panierRep,EntityManagerInterface $manager,$total): Response
    {
        $user=$this->getUser();
        $panier=$panierRep->findOneBy(['user'=>$user]);

        $commande= new Commande();
        $commande->setUser($user);

        foreach ($panier->getLignePaniers() as $lignePanier) {
            $ligneCommande = new LigneCommande();
            $ligneCommande->setCommande($commande);
            $ligneCommande->setLivre($lignePanier->getLivre());
            $ligneCommande->setQuantite($lignePanier->getQuantite());
            
            $manager->persist($ligneCommande);
            $commande->addLigneCommande($ligneCommande);
            
        }

        $manager->persist($commande);
        $manager->flush();

        foreach ($panier->getLignePaniers() as $lignePanier) {
            $manager->remove($lignePanier);
        }
        $manager->flush();


        return $this->redirectToRoute('afficher_commande', ['id' => $commande->getId(),'total'=>$total]);
    }

    #[Route('/profile/commande/afficher/{id}/{total}', name: 'afficher_commande')]
    public function afficherCommande(int $id,$total, CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find($id);

        return $this->render('commande/index.html.twig', [
            'commande' => $commande,
            'total'=>$total
        ]);
    }

    #[Route('/admin/commande/delete/{id}', name: 'admin_commande_delete')]
    public function delete(EntityManagerInterface $em,Commande $commande): Response
    {
        
        $em->remove($commande);
        $em->flush();
        return $this->redirectToRoute('list_commande');
        
    }

    #[Route('/admin/commande', name: 'list_commande')]
    public function listCommande(CommandeRepository $commandeRepository,Request $request,PaginatorInterface $paginator,): Response
    {
        $commandes = $commandeRepository->findAll();
        $queryBuilder = $commandeRepository->createQueryBuilder('c')
        ->leftJoin('c.user', 'u')
        ->addSelect('u');
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            3
        );
        return $this->render('commande/list.html.twig', [
             
            'pagination'=>$pagination
        ]);
    }

}