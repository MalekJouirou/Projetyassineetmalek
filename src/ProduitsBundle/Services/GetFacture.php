<?php

namespace ProduitsBundle\Services;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Dompdf\Dompdf;
use Dompdf\Options;

class GetFacture
{
    public function __construct(ContainerInterface $container)
    {

        $this->container = $container;
        
        //$this->securityContext = $container->get('security.token_storage');
    }

    public function facture($facture)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->container->get('templating')->render('@Produits/produits/facturePDF.html.twig', [
            'facture' => $facture
        ]);
        
        
       // Load HTML to Dompdf
       $dompdf->loadHtml($html);
        
       // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
       $dompdf->setPaper('A4', 'portrait');
       

       // Render the HTML as PDF
       $dompdf->render();

       // Output the generated PDF to Browser (inline view)
       $dompdf->stream("myFacture.pdf", [
           "Attachment" => false
       ]);


    }
}