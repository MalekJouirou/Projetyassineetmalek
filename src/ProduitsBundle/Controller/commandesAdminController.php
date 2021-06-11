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





class CommandesAdminController extends Controller
{

   public function commandesAction()
   {
    $dm = $this->getDoctrine()->getManager();
    $commandes = $dm->getRepository('ProduitsBundle:Commandes')->findAll();

    return $this->render('@Produits/Administration/Commandes/Layout/index.html.twig', array(
        'commandes' => $commandes
    ));
   }  

   public function showFactureAction($id)
    {
        $dm = $this->getDoctrine()->getManager();
        $facture = $dm->getRepository('ProduitsBundle:Commandes')->find($id);

        if(!$facture) {
            $this->get('session')->getFlashBag()->add('error', 'une erreur est survenue');
            return $this->redirect($this->generateUrl('adminCommande'));

        } 
        
       $this->container->get('setNewFacture')->facture($facture);

    }


}