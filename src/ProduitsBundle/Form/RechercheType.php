<?php

namespace ProduitsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RechercheType extends AbstractType 
{
    public function buildForm(FormbuilderInterface $builder, array $option)
    {
        $builder->add('recherche','text', array('label' => false,
                                       'attr' => array('class' => 'form-control')));
    }

    public function getName()
    {
        return 'produitsbundle_recherche';
    }
}