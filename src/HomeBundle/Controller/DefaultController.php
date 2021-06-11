<?php

namespace HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    
    public function indexAction()
     
    {
        return $this->render('@Home/Default/index.html.twig', array(
            'mavariable'=>'ma variable value'
        ));
    }
     
    
    public function heritageAction()
    {
        return $this->render('@Home/Default/heritage.html.twig');
    }
    
  
}

