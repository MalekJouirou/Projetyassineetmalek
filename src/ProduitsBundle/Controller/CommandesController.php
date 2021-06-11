<?php

namespace ProduitsBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProduitsBundle\Entity\Produits;
use ProduitsBundle\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use ProduitsBundle\Form\RechercheType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use ProduitsBundle\Form\UtilisateursAdressesType;
use ProduitsBundle\Entity\UtilisateursAdresses;
use ProduitsBundle\Entity\Task;
use ProduitsBundle\Entity\Commandes;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Dompdf\Dompdf;
use Dompdf\Options;





class CommandesController extends Controller
{

    public function facture(Request $request)
    {
        $dm = $this->getDoctrine()->getManager();
       // $generator = $this->container->get('security.secure_random');
        //$generator = random_bytes(20);
        $random = random_int(1, 20);
        $session = $this->get('session'); 
        $adresse = $session->get('adresse');
       // $panier = $session->get('panier');
       $panier = $session->get('panier'); 
        $commande = array();  // on déclare un tableau commande
        $totalHT = 0;
        
        $livraison = $dm->getRepository('ProduitsBundle:UtilisateursAdresses')->find($adresse['livraison']);
        $facturation = $dm->getRepository('ProduitsBundle:UtilisateursAdresses')->find($adresse['facturation']);
       // $produits = $dm->getRepository('ProduitsBundle:Produits')->findArray(array_keys($session->get('panier')));
       $produits=array();
       foreach(array_keys($session->get('panier')) as $prod){
           $produits[]=$dm->getRepository('ProduitsBundle:Produits')->find($prod);
       }

   
       foreach($produits as $produit)
       {
        $prixHT = ($produit->getPrix() * $panier[$produit->getId()]);
        $totalHT += $prixHT;

        $commande['produit'][$produit->getId()] = array('reference' => $produit->getNom(),
                                                        'quantite' => $panier[$produit->getId()],
                                                        'prixHT' => round($produit->getPrix(),2));
       }                                                

        $commande['livraison'] = array(  'prenom' => $livraison->getPrenom(),
                                        'nom' => $livraison->getNom(),       
                                        'telephone' => $livraison->getTelephone(),
                                        'adresse' => $livraison->getAdresse(),
                                        'cp' => $livraison->getCp(),
                                        'ville' => $livraison->getVille(),
                                        'pays' => $livraison->getPays(),
                                        'complement' =>$livraison->getComplement() );

        $commande['facturation'] = array('prenom' => $facturation->getPrenom(),
                                        'nom' => $facturation->getNom(),       
                                        'telephone' => $facturation->getTelephone(),
                                        'adresse' => $facturation->getAdresse(),
                                        'cp' => $facturation->getCp(),
                                        'ville' => $facturation->getVille(),
                                        'pays' => $facturation->getPays(),
                                        'complement' =>$facturation->getComplement() );  
        
        $commande['prixHT'] = round($totalHT,2);           
       $commande['token'] = bin2hex($random);
        
        return $commande;
                                                        
        
                                                        

       

}
    public function prepareCommandeAction(Request $request)
    {
         // on préstocke les données du panier
        $session = $this->get('session');
        $dm = $this->getDoctrine()->getManager();

        if(!$session->has('commande')) 
        $commande = new Commandes();
        
        

        else 
        $commande = $dm->getRepository('ProduitsBundle:Commandes')->find($session->get('commande'));
    
        $commande->setDate(new \DateTime());
        //$commande->setUtilisateur($this->getUser());
        
       
        $commande->setUtilisateur($this->container->get('security.token_storage')->getToken()->getUser()); 
        $commande->setValider(0);
        $commande->setReference(0);
        $commande->setCommande($this->facture($request)); // on stocke toute la commande à partir de la méthode facture

        if(!$session->has('commande')) { 
            $dm->persist($commande);
           $session->set('commande', $commande);
        
        }
       
        $dm->flush();
        return new Response($commande->getId());

    }
/*
cette methode remplace l'api banque
*/
    public function validationCommandeAction($id) {
   
        $dm = $this->getDoctrine()->getManager();
        $commande = $dm->getRepository('ProduitsBundle:Commandes')->find($id);
        
        if(!$commande || $commande->getValider() == 1 )
        throw $this->createNotFoundException('La commande n\'existe pas');

        $commande->setValider(1);
        $commande->setReference($this->container->get('setNewReference')->reference()); //Service

        $dm->flush();

        $session = $this->get('session');
        $session->remove('adresse');
        $session->remove('panier');
        $session->remove('commande');


//ici le mail de validation 
$message = \Swift_Message::newInstance()
        ->setSubject('validation de votre commande')
        ->setFrom(array('yassinekarkar001@gmail.com' => "ArabCom"))
        ->setTo($commande->getUtilisateur()->getEmailCanonical())
        ->setCharset('utf-8')
        ->setContentType('text/html')
        ->setBody($this->renderView('@Produits/Administration/SwiftLayout/validationCommande.html.twig',array('utilisateur' => $commande->getUtilisateur())));
        $this->get('mailer')->send($message);

        $this->get('session')->getFlashBag()->add('success','Votre commande est validé avec succès');

        return $this->redirect($this->generateUrl('factures'));


    }


    public function facturesAction()
    {
        $dm = $this->getDoctrine()->getManager();
        $factures = $dm->getRepository('ProduitsBundle:Commandes')->byFacture($this->getUser());

        return $this->render('@Produits/produits/facture.html.twig', array('factures' => $factures));
    }
 
    public function facturePDFAction($id)
    {
        $dm = $this->getDoctrine()->getManager();
        $facture = $dm->getRepository('ProduitsBundle:Commandes')->findOneBy(array('utilisateur' => $this->getUser(),
                                                                            'valider' => 1,
                                                                            'id' => $id));

        if(!$facture) {
            $this->get('session')->getFlashBag()->add('error', 'une erreur est survenue');
            return $this->redirect($this->generateUrl('factures'));

        } 
        
       $this->container->get('setNewFacture')->facture($facture);

    }



    

}