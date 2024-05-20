<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Snappy\Pdf;
use App\service\PdfService;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PayementController extends AbstractController
{
    #[Route('/profile/payement', name: 'app_payement')]
    public function index(): Response
    {
        return $this->render('payement/index.html.twig', [
            'controller_name' => 'PayementController',
        ]);
    }
    #[Route('/profile/checkout/{total}/{commande}', name: 'checkout')]
    public function checkout($stripeSK,$total,int $commande,SessionInterface $session)
    {
      $session->set('commandeId', $commande);
      $session->set('total', $total);
        $stripe = new \Stripe\StripeClient($stripeSK);
        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => [[
              'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                  'name' => 'Montant total',
                ],
                'unit_amount' => $total,
              ],
              'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url',[],UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url',[],UrlGeneratorInterface::ABSOLUTE_URL),
          ]);

          return $this->redirect($checkout_session->url,303);
    }
    #[Route('/success_url', name: 'success_url')]
    public function successUrl(SessionInterface $session,CommandeRepository $comRep,EntityManagerInterface $manager): Response
    {
      $commande = $comRep->find($session->get('commandeId'));
      $total= $session->get('total');
      foreach ($commande->getLigneCommandes() as $ligneCommande) {
    
       $livre= $ligneCommande->getLivre();
       $Quantite=$ligneCommande->getQuantite();
       $livre->setQte($livre->getQte()-$Quantite);

      
        
        $manager->persist($livre);
        $manager->flush();
    }
        return $this->render('payement/success.html.twig',['total'=>$total,'commande'=>$commande->getId()]);
    }
    #[Route('/cancel_url', name: 'cancel_url')]
    public function cancelUrl(): Response
    {
        return $this->render('payement/cancel.html.twig',[]);
    }
    #[Route('/pdf/{total}/{commande}', name: 'commande_pdf')]
    public function generatePDF($total, int $commande, CommandeRepository $commandeRepository): Response
    {
      $c = $commandeRepository->find($commande);
       
    
        
        
        $html = $this->renderView('commande/commandepdf.html.twig',['total'=>$total,'commande'=>$c]);
    
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
    
        $pdfOutput = $dompdf->output();
    
        return new Response(
            $pdfOutput,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="resume.pdf"'
            ]
        );
    }
    

    
  
}