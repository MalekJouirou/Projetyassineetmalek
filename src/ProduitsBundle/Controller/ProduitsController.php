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

class ProduitsController extends Controller
{
public function categorieAction() 
{
    /*
    //appeler le gestionnaire de Doctrine
    $dm = $this->getDoctrine()->getManager();
    
    $produits = $dm->getRepository('ProduitBundle:Produits')->parCategorie('parfum');
    
    return $this->render('ProduitsBundle:')
    

    */ 
    
}

    public function indexAction()
    {
      
      // retourner l'affichage de la page index.html.twig qui sze trouve dans le dossier "produits" sous "views"
       $dm = $this->getDoctrine()->getManager();
      
       
       $categories = $dm->getRepository('ProduitsBundle:Categories')->findAll();
       

      
               
        return $this->render('@Produits/produits/index.html.twig', [
            'categories' => $categories
        ]);
    }
    
    public function presentationAction($id)
    {
     $session = $this->get('session');
     $dm = $this->getDoctrine()->getManager();
     
     //$article_5 = $repository->find(5);
     $produit = $dm->getRepository('ProduitsBundle:Produits')
      ->find($id);

      if($session->has('panier'))
        $panier = $session->get('panier');
      else
        $panier = false;     

                
        return $this->render('@Produits/produits/presentation.html.twig',
                array('produit'=> $produit , 'panier'=> $panier)
                );  
        
    }
     public function panierAction(SessionInterface $session)
    {
      // retourner l'affichage de la page panier.html.twig qui sze trouve dans le dossier "produits" sous "views"
    $panier = $session->get('panier', []); 
      $panierWithData = [];
      $dm = $this->getDoctrine()->getManager();
foreach($panier as $id => $quantity) {
  $panierWithData[] = [ 
    
    'product' => $dm->getRepository('ProduitsBundle:Produits')
    ->find($id),
    'quantity' => $quantity 
  ];
}
$total = 0;
foreach($panierWithData as $item) {
  $totalItem = $item['product']->getPrix() * $item['quantity'];
  $total += $totalItem;
}

$totalquantity = 0;
foreach($panierWithData as $item) {
   $item['quantity'];
   $totalquantity +=  $item['quantity']; ;
  }
      return $this->render('@Produits/produits/panier.html.twig' , 
               array('items' => $panierWithData,  
                     'total' => $total , 'totalquantity'=> $totalquantity));

      return $this->render('@Produits/produits/panier.html.twig');
      } 

     public function livraisonAdresseAction(Request $request)
    {
      // retourner l'affichage de la page adresseLivraison.html.twig qui sze trouve dans le dossier "produits" sous "views"
    //  $utilisateur = $this->getUser(); 
      $utilisateur = $this->container->get('security.token_storage')->getToken()->getUser();
      $useradresse = $this->getDoctrine()->getManager()
      ->getRepository('ProduitsBundle:UtilisateursAdresses')->findByUtilisateur($utilisateur->getId());
  //    exit(var_dump($useradresse));
      //security.context
      $entity = new UtilisateursAdresses();
        //$form = $this->createForm(new UtilisateursAdressesType() , $entity);
        $form = $this->createForm(UtilisateursAdressesType::class, $entity);

        if ($request->isMethod("POST")) {
          
          $form->handleRequest($request);
          if ($form->isValid()){
            $dm = $this->getDoctrine()->getManager();
            $entity->setUtilisateur($utilisateur);
            $dm->persist($entity);
            $dm->flush();

            return $this->redirect($this->generateUrl('produits_livraisonAdresse'));
            
          }
        }

        

      return $this->render('@Produits/produits/livraisonAdresse.html.twig', array(
                                                                    'utilisateur' => $utilisateur,
                                                                    'useradresse' => $useradresse,
                                                                     'form' => $form->createView()));
    }

    public function adresseSupressionAction($id){ 
      $dm = $this->getDoctrine()->getManager();
      $entity = $dm->getRepository('ProduitsBundle:UtilisateursAdresses')->find($id);
      if ($this->getUser() != $entity->getUtilisateur() || !$entity)
        return $this->redirect($this->generateUrl('produits_livraisonAdresse'));
        $dm->remove($entity);
        $dm->flush();
      return $this->redirect($this->generateUrl('produits_livraisonAdresse'));
       
    }
   
    

    public function setLivraisonOnSession(Request $request)
    {
      $session = $this->get('session');
 
        if(!$session->has('adresse')) $session->set('adresse', array());
        $adresse = $session->get('adresse');


        if ($request->request->get('livraison') !=null && $request->request->get('facturation') !=null )  {
          $adresse['livraison'] = $request->request->get('livraison') ;
          $adresse['facturation'] = $request->request->get('livraison') ;
          
        }else {
          return $this->redirect($this->generateUrl('produits_validation'));
        }

      $session->set('adresse', $adresse);
           
      return $this->redirect($this->generateUrl('produits_validation'));
    }
      public function validationAction(Request $request)
    {
      
     
      if ($request->isMethod("POST")) 
         $this->setLivraisonOnSession($request);

    $dm = $this->getDoctrine()->getManager();
    $prepareCommande = $this->forward('ProduitsBundle:Commandes:prepareCommande');
    $commande = $dm->getRepository('ProduitsBundle:Commandes')->find($prepareCommande->getContent());
    
  

      // retourner l'affichage de la page validation.html.twig qui se trouve dans le dossier "produits" sous "views"
        return $this->render('@Produits/produits/validation.html.twig',array('commande' => $commande));
                                                                              
    }
    public function inscriptionAction()
    {
      // retourner l'affichage de la page inscription.html.twig qui sze trouve dans le dossier "produits" sous "views"
        return $this->render('@Produits/produits/inscription.html.twig');
    }
        public function profileAfficherAction()
    {
      // retourner l'affichage de la page afficheProfile.html.twig qui sze trouve dans le dossier "produits" sous "views"
        return $this->render('@Produits/produits/profileAfficher.html.twig');
    }
      public function profileEditAction()
    {
      // retourner l'affichage de la page editeProfile.html.twig qui sze trouve dans le dossier "produits" sous "views"
        return $this->render('@Produits/produits/profileEdit.html.twig');
    }
     public function loginAction()
    {
        return $this->render('@Produits/produits/login.html.twig');
    }
     public function produitAction()
    {
        $session = $this->get('session');     
        $dm = $this->getDoctrine()->getManager();
        $produitsAll = $dm->getRepository('ProduitsBundle:Produits')
                ->findAll();
        if($session->has('panier'))
        $panier = $session->get('panier');
        else
        $panier = false;        
                 
        //$produits = $this->get('knp_paginator')->paginate($produitsAll,$this->get('request')->query->get('page', 1),2);
        return $this->render('@Produits/produits/produit.html.twig',
                array('produits'=> $produitsAll,
                'panier'=> $panier)
                );
    }
       public function profileCommandeAction()
    {
        return $this->render('@Produits/produits/profileCommande.html.twig');
    }
       public function profileCreditAction()
    {
        return $this->render('@Produits/produits/profileCredit.html.twig');
    }
       public function profileFavorisAction()
    {
        return $this->render('@Produits/produits/profileFavoris.html.twig');
    }
       public function profileFavorisVideAction()
    {
        return $this->render('@Produits/produits/profileFavorisVide.html.twig');
    }
       public function profileFavorisCommandeVideAction()
    {
        return $this->render('@Produits/produits/profileFavorisCommandeVide.html.twig');
    }
       public function profileCreditVideAction()
    {
        return $this->render('@Produits/produits/profileCreditVide.html.twig');
    }
      public function profileEditPasswordAction()
    {
        return $this->render('@Produits/produits/profileEditPassword.html.twig');
    }
    public function profilePasswordPerduAction()
    {
        return $this->render('@Produits/produits/profilePasswordperdu.html.twig');
    }
     public function profileModifierAdresseAction()
    {
        return $this->render('@Produits/produits/profileModifierAdresse.html.twig');
    }
    /*
     * id=1 => materiels et circuits
     * id=2 => Microcontrôleur (8 à 32 bits)
     * id=3 => Microprocesseur
     * id=4 => accessoire
     * id=5 => parfum
     * id=6 => cheuveux
     */
      public function produitsMicrocontroleurAction()
    {
         $dm = $this->getDoctrine()->getManager();
         $microcontroleur= $dm->getRepository('ProduitsBundle:Produits')
                     ->findBy(array('categorie'=>'2'));
        return $this->render('@Produits/produits/produit.html.twig',
                array('produits'=> $microcontroleur )
                );
    }
      public function produitsCercuitsAction()
    {
         $dm = $this->getDoctrine()->getManager();
         $materielsetcircuits= $dm->getRepository('ProduitsBundle:Produits')
                     ->findBy(array('categorie'=>'1'));
        return $this->render('@Produits/produits/produit.html.twig',
                array('produits'=> $materielsetcircuits)
                );
    }
      public function produitsMicroprocesseurAction()
    {
         $dm = $this->getDoctrine()->getManager();
         $produitsMicroprocesseur= $dm->getRepository('ProduitsBundle:Produits')
                     ->findBy(array('categorie'=>'3'));
        return $this->render('@Produits/produits/produit.html.twig',
                array('produits'=> $produitsMicroprocesseur)
                );
    }
      public function produitsMaquillageAction()
    {
         $dm = $this->getDoctrine()->getManager();
         $produitsMaquillage= $dm->getRepository('ProduitsBundle:Produits')
                     ->findBy(array('categorie'=>'3'));
        return $this->render('@Produits/produits/produit.html.twig',
                array('produits'=> $produitsMaquillage)
                );
    }
      public function produitsSoinVisageAction()
    {
         $dm = $this->getDoctrine()->getManager();
         $produitsSoinVisage= $dm->getRepository('ProduitsBundle:Produits')
                     ->findBy(array('categorie'=>'2'));
        return $this->render('@Produits/produits/produit.html.twig',
                array('produits'=> $produitsSoinVisage)
                );
    }
      public function produitsnouveauteeAction()
    {
         $dm = $this->getDoctrine()->getManager();
         $produitsNouveautee= $dm->getRepository('ProduitsBundle:Produits')
                     ->findBy(array('categorie'=>'nouveautee'));
        return $this->render('@Produits/produits/produit.html.twig',
                array('produits'=> $produitsNouveautee)
                );
    }
      public function produitsPopulaireAction()
    {
         $dm = $this->getDoctrine()->getManager();
         $produitsPopulaire= $dm->getRepository('ProduitsBundle:Produits')
                     ->findAll();
        return $this->render('@Produits/produits/produit.html.twig',
                array('produits'=> $produitsPopulaire)
                );
    }
    //recuperation de tous les catégories---------------------------------------
    /* public function produitRecuperationAction($categorie)
    {
         $dm = $this->getDoctrine()->getManager();
        $produitsBycategorie = $dm->getRepository('ProduitsBundle:Produits')
                ->findBy(array('categorie'=>$categorie));
                 
        return produitsBycategorie;
                
    } */
   /* public function produitRecuperationAction($categorie)
    {
         $dm = $this->getDoctrine()->getManager();
        $produitsBycategorie = $dm->getRepository('ProduitsBundle:Produits')
                ->findBy(array('categorie'=>$categorie));
                 
        return produitsBycategorie;
                
    }*/



/*public function rechercheAction(){
  $formBuilder = $this->createFormBuilder(new RechercheType());
  
  $form = $formBuilder->getForm();
  /*$form= $this->createForm(new RechercheType());*/
  /*return $this->render('@Produits/Recherche/moduleUsed/recherche.html.twig',
  array('form'=> $form->createView())
  ); 
}
  public function rechercheTraitementAction(Request $request)
  {
   /* $form = $this->createFormBuilder()->add('recherche')->getForm();*/
  /* $form = $this->createFormBuilder(new RechercheType());


    if ($request->isMethod("POST"))
    {
    $form->handleRequest($request);
    
    
    $dm = $this->getDoctrine()->getManager();
     
    
    $produit = $dm->getRepository('ProduitsBundle:Produits')
     ->recherche($form['recherche']->getData());
    }           
       return $this->render('@Produits/produits/produit.html.twig',
               array('produit'=> $produit)
               );  
       
  } */
   
    
    public function rechercheProduitAction(Request $request){
        $dm = $this->getDoctrine()->getManager();
         if($request->isMethod("POST")){
             $nom=$request->get('nom');
             $produits = $dm->getRepository('ProduitsBundle:Produits')
                ->findBy(array('nom'=>$nom));
         }
         return $this->render('@Produits/produits/produit.html.twig',
                array('produits'=> $produits ,'nom'=>$nom)
                );  
    }
    
   /* 
    public function rechercheByCategorieAction(Request $request){
        $dm = $this->getDoctrine()->getManager();
         $produits = $dm->getRepository('ProduitsBundle:Produits')
                ->findAll();
         if($request->isMethod("POST")){
             $categorie=$request->get('nom');
             $produits = $dm->getRepository('ProduitsBundle:Produits')
                ->findBy(array('nom'=>$categorie));
         }
         return $this->render('@Produits/produits/produit.html.twig',
                array('produits'=> $produits)
                );  
    }
*/
    

    /*la premier methode pour ajouter un produit dans le panier*/
  /*public function addAction($id , SessionInterface $session){

       $panier = $session->get('panier', []);
       if(!empty($panier[$id])) {
        $panier[$id] ++;
       } else {
        $panier[$id] = 1;
       }
      
       $session->set('panier' ,$panier);
      
       return $this->redirectToRoute("produits_panier");
     
   }*/

public function removeAction($id , SessionInterface $session){
  $panier = $session->get('panier', []);
  if(!empty($panier[$id])){
    unset($panier[$id]);
  }
  $session->set('panier' ,$panier);
      
$this->get('session')->getFlashBag()->add('success','Article supprimé avec succès');  
  return $this->redirectToRoute("produits_panier");

}   

  public function addAction($id , Request $request)
    {
        
      $session = $this->get('session');
 
        if(!$session->has('panier')) $session->set('panier', array());
        $panier = $session->get('panier');

      /*if(!empty($panier[$id])) {
        $panier[$id] ++;

      }elseif($request->request->get('qte') != null){
       
        $panier[$id] = $request->request->get('qte');

      } else {
        $panier[$id] = 1;
      }*/
    
     if ($panier[$id] = ($request->request->has('qte'))) {
       $panier[$id] = $request->request->get('qte') ;
      $this->get('session')->getFlashBag()->add('success','Quantité modifié avec succès');
      }else{
        $panier[$id] = 1;
      $this->get('session')->getFlashBag()->add('success','Article ajouté avec succès');
      }
      
  //  $panier[$id]=($request->request->has('qte')) ? $request->request->get('qte') : 1;
        
            $session->set('panier', $panier);
           
            
          return $this->redirect($this->generateUrl('produits_panier'));
    }

  public function menuAction()
  {
    $session = $this->get('session');
    if(!$session->has('panier'))
    $articles = 0;
    else 
    $articles = count($session->get('panier'));

    return $this->render('@Produits/produits/menu.html.twig',
      array('articles'=> $articles )
      );  
     
  }  

 
   //Contact page
   public function contactAction(Request $request)
   {
    $utilisateur = $this->container->get('security.token_storage')->getToken()->getUser();

if ($request->isMethod("POST")) {
  $name = $request->get("name");
  $last = $request->get("last");
  $email = $utilisateur->getEmailCanonical();
  $Subject = $request->get("sujet");
  $message = $request->get("message");



  //$mailer = $this->container->get('mailer');
  //$transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
    // ->setUsername('yassinekarkar001@gmail.com')
//->setPassword('PFE20202021');
   //  $mailer = \Swift_Mailer::newInstance($transport);
     $message = \Swift_Message::newInstance()
     ->setFrom($email)
     ->setTo('yassinekarkar001@gmail.com')
     ->setSubject($Subject)
     ->setBody($message);
     $this->get('mailer')->send($message);
}


     // retourner l'affichage de la page contact.html.twig qui sze trouve dans le dossier "produits" sous "views"
       return $this->render('@Produits/produits/contact.html.twig');
   }


   public function panierlivraisonAction(SessionInterface $session)
   {
     // retourner l'affichage de la page panier.html.twig qui sze trouve dans le dossier "produits" sous "views"
   $panier = $session->get('panier', []); 
     $panierWithData = [];
     $dm = $this->getDoctrine()->getManager();
foreach($panier as $id => $quantity) {
 $panierWithData[] = [ 
   
   'product' => $dm->getRepository('ProduitsBundle:Produits')
   ->find($id),
   'quantity' => $quantity 
 ];
}
$total = 0;
foreach($panierWithData as $item) {
 $totalItem = $item['product']->getPrix() * $item['quantity'];
 $total += $totalItem;
}

$totalquantity = 0;
foreach($panierWithData as $item) {
  $item['quantity'];
 $totalquantity +=  $item['quantity']; ;
}

if(!$session->has('panier'))
    $articles = 0;
    else 
    $articles = count($session->get('panier'));


       return $this->render('@Produits/produits/navCommande.html.twig' , 
       array('items' => $panierWithData,  
     'total' => $total , 'totalquantity'=> $totalquantity , 'articles' => $articles));

   }
   

   public function aproposAction()
   {
    return $this->render('@Produits/produits/apropos.html.twig');
   }
    
    
}








