<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\LignePanier;
use App\Repository\LivresRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    return $this->render('panier/index.html.twig', [
        'panier' => $panier,
        'total' => $total,
    ]);
}


}
