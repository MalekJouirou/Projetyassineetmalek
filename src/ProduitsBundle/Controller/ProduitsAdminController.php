<?php

namespace ProduitsBundle\Controller;

use ProduitsBundle\Entity\Produits;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Produit controller.
 *
 */
class ProduitsAdminController extends Controller
{
    /**
     * Lists all produit entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('ProduitsBundle:Produits')->findAll();

        return $this->render('@Produits/Administration/Produits/Layout/index.html.twig', array(
            'produits' => $produits,
        ));
    }

    /**
     * Creates a new produit entity.
     *
     */
    public function newAction(Request $request)
    {
        $produit = new Produits();
        $form = $this->createForm('ProduitsBundle\Form\ProduitsType', $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $produit->uploadProfilePicture();
            $em->persist($produit);
            $em->flush();

            return $this->redirectToRoute('adminProduits_show', array('id' => $produit->getId()));
        }

        return $this->render('@Produits/Administration/Produits/Layout/new.html.twig', array(
            'produit' => $produit,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a produit entity.
     *
     */
    public function showAction(Produits $produit)
    {
        $deleteForm = $this->createDeleteForm($produit);

        return $this->render('@Produits/Administration/Produits/Layout/show.html.twig', array(
            'produit' => $produit,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing produit entity.
     *
     */
    public function editAction(Request $request, Produits $produit)
    {
        $deleteForm = $this->createDeleteForm($produit);
        $editForm = $this->createForm('ProduitsBundle\Form\ProduitsType', $produit);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('adminProduits_edit', array('id' => $produit->getId()));
        }

        return $this->render('@Produits/Administration/Produits/Layout/edit.html.twig', array(
            'produit' => $produit,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a produit entity.
     *
     */
    public function deleteAction(Request $request, Produits $produit)
    {
        $form = $this->createDeleteForm($produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($produit);
            $em->flush();
        }

        return $this->redirectToRoute('adminProduits_index');
    }

    /**
     * Creates a form to delete a produit entity.
     *
     * @param Produits $produit The produit entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Produits $produit)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('adminProduits_delete', array('id' => $produit->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
