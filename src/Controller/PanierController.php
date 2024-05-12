<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\LignePanier;
use App\Repository\LivresRepository;
use App\Repository\PanierRepository;
use App\Repository\CodePromoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    #[Route('/profile/panier/{id}', name: 'panier_ajouter')]
    public function ajouterAuPanier(int $id, LivresRepository $livrep, PanierRepository $panierRep,EntityManagerInterface $manager): Response
    {
        $livre=$livrep->find($id);
        $user = $this->getUser();
        $panier = $panierRep->findOneBy(['user'=>$user]);

        if (!$panier) {
            $panier = new Panier();
            $panier->setUser($user);
        }

        $lignePanier = null;
        foreach ($panier->getLignePaniers() as $ligne) {
            if ($ligne->getLivre()->getId() === $livre->getId()) {
                $lignePanier = $ligne;
                break;
            }
        }

        if ($lignePanier) {
            $lignePanier->setQuantite($lignePanier->getQuantite() + 1);
        } else{
        $lignePanier = new LignePanier();
        $lignePanier->setLivre($livre);
        $lignePanier->setQuantite(1);

        
        $manager->persist($lignePanier);
        $panier->addLignePanier($lignePanier);
    }
        $manager->persist($panier);
        $manager->flush();

        return $this->redirectToRoute('panier_afficher');
    }



    #[Route('/profile/panier/{id}/retirer', name: 'panier_retirer_un')]
    public function retirerUnDuPanier(int $id, LivresRepository $livrep, PanierRepository $panierRep, EntityManagerInterface $manager): Response
    {
        $livre = $livrep->find($id);
        $user = $this->getUser();
        $panier = $panierRep->findOneBy(['user' => $user]);

        if ($panier) {
            $lignePanier = null;
            foreach ($panier->getLignePaniers() as $ligne) {
                if ($ligne->getLivre()->getId() === $livre->getId()) {
                    $lignePanier = $ligne;
                    break;
                }
            }

            if ($lignePanier && $lignePanier->getQuantite() > 1) {
                $lignePanier->setQuantite($lignePanier->getQuantite() - 1);
            } elseif ($lignePanier && $lignePanier->getQuantite() == 1) {
                $panier->removeLignePanier($lignePanier);
                $manager->remove($lignePanier);
            } else {
                $this->addFlash('error', 'La quantité ne peut pas être négative.');
            }

            $manager->flush();
        }

        return $this->redirectToRoute('panier_afficher');
    }


    #[Route('/profile/panier', name: 'panier_afficher')]
    public function afficherPanier(PanierRepository $panierRepository): Response
    {
        $user = $this->getUser(); 
        $panier = $panierRepository->findOneBy(['user' => $user]);
    
        $total = 0;
        foreach ($panier->getLignePaniers() as $lignePanier) {
            $total += $lignePanier->getLivre()->getPrix() * $lignePanier->getQuantite();
        }
    
        $totavantRed = $total;
        $totapresRed = null; 
    
        if ($panier->getCodePromo()) {
            $totapresRed = $totavantRed - $totavantRed * ($panier->getCodePromo()->getReduction() / 100);
        }
    
        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'totalAvantReduction' => $totavantRed,
            'totalApresReduction' => $totapresRed,
        ]);
    }
    #[Route('/profile/total-panier', name: 'total_panier')]
    public function totalPanier(PanierRepository $panierRepository): Response
    {
        $user = $this->getUser(); 
        $panier = $panierRepository->findOneBy(['user' => $user]);
    
        $total = 0;
        if ($panier) {
            foreach ($panier->getLignePaniers() as $lignePanier) {
                $total += $lignePanier->getQuantite();
            }
        }
        
        return new Response($total);
    }
    

    


    #[Route('/profile/panier/codepromo/appliquer', name: 'panier_codepromo')]
    public function appliquerCodePromo(Request $request, CodePromoRepository $codePromoRepository, PanierRepository $panierRepository, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser(); 
        $panier = $panierRepository->findOneBy(['user' => $user]);

        $codePromo = $request->get('codepromo');
        $code = $codePromoRepository->findOneBy(['code' => $codePromo]);

        if ($code) {
            $panier->setCodePromo($code);
            $manager->persist($panier);
            $manager->flush();

            $this->addFlash('success', 'Code promo appliqué avec succès!');
            $total = 0;
        foreach ($panier->getLignePaniers() as $lignePanier) {
            $total += $lignePanier->getLivre()->getPrix() * $lignePanier->getQuantite();
        }
           
        } else {
            $this->addFlash('error', 'Code promo invalide.');
        }
        

        return $this->redirectToRoute('panier_afficher');
    }
    #[Route('/profile/panier/{id}/supprimer', name: 'panier_supprimer_ligne')]
    public function supprimerLigneDuPanier(int $id, LivresRepository $livrep, PanierRepository $panierRep, EntityManagerInterface $manager): Response
    {
        $livre = $livrep->find($id);
        $user = $this->getUser();
        $panier = $panierRep->findOneBy(['user' => $user]);

        if ($panier) {
            $lignePanier = null;
            foreach ($panier->getLignePaniers() as $ligne) {
                if ($ligne->getLivre()->getId() === $livre->getId()) {
                    $lignePanier = $ligne;
                    break;
                }
            }

            if ($lignePanier) {
                $panier->removeLignePanier($lignePanier);
                $manager->remove($lignePanier);
                $panier->setCodePromo(null);
                $manager->flush();
            }
        }

        return $this->redirectToRoute('panier_afficher');
    }

        

}
