<?php

namespace ProduitsBundle\Entity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Images
 *
 * @ORM\Table(name="images")
 * @ORM\Entity
 */
class Images
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=100, nullable=false)
     */
    private $path;

    
    /* nous faisons une jointure bidirectionnel entre le table Images et table produits pour que indiquer dans le champ produit le produit correspent à cette image
     * et nous appeler la jointure par le nom produit 
     */
    
    /**
     *
     *  @ORM\ManyToOne(targetEntity="Produits", inversedBy="produit")
     */
    private $produit;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Images
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set produit
     *
     * @param string $produit
     *
     * @return Images
     */
    public function setProduit($produit)
    {
        $this->produit = $produit;

        return $this;
    }

    /**
     * Get produit
     *
     * @return string
     */
    public function getProduit()
    {
        return $this->produit;
    }
}

