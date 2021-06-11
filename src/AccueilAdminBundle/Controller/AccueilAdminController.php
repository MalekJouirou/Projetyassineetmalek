<?php

namespace AccueilAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccueilAdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('@AccueilAdmin/Accueil/index.html.twig');
    }
   
   
      public function afficherProfilAction()
    {
        return $this->render('@AccueilAdmin/Accueil/afficherProfil.html.twig');
    }
   
      public function modifierProfilAction()
    {
        return $this->render('@AccueilAdmin/Accueil/modifierProfil.html.twig');
    }
     public function gestionCategorieAction()
    {
        return $this->render('@AccueilAdmin/Accueil/gestionCategorie.html.twig');
    }
      public function gestionUtilisateurAction()
    {
        return $this->render('@AccueilAdmin/Accueil/gestionUtilisateur.html.twig');
    }
      public function gestionProduitAction()
    {
        return $this->render('@AccueilAdmin/Accueil/gestionProduit.html.twig');
    }
    
}