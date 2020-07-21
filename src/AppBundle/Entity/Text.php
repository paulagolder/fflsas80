<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TextRepository")
 */
class Text
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $objecttype;

    /**
     * @ORM\Column(type="smallint")
     */
    private $objid;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $attribute;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $language;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $contributor;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $update_dt;
    
    
     public $label;
    
    public $link;

    public function getId()
    {
        return $this->id;
    }

    public function getObjecttype(): ?string
    {
        return $this->objecttype;
    }

    public function setObjecttype(string $objecttype): self
    {
        $this->objecttype = $objecttype;

        return $this;
    }

    public function getObjid(): ?int
    {
        return $this->objid;
    }

    public function setObjid(int $objid): self
    {
        $this->objid = $objid;

        return $this;
    }

    public function getAttribute(): ?string
    {
        return $this->attribute;
    }

    public function setAttribute(string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getContributor(): ?string
    {
        return $this->contributor;
    }

    public function setContributor(?string $contributor): self
    {
        $this->contributor = $contributor;

        return $this;
    }

    public function getUpdateDt(): ?\DateTimeInterface
    {
        return $this->update_dt;
    }

    public function setUpdateDt(?\DateTimeInterface $update_dt): self
    {
        $this->update_dt = $update_dt;

        return $this;
    }
    
    
    public function findText($text_ar,$attribute, $lang)
    {
             if( array_key_exists ( $lang ,  $text_ar[$attribute]))
                $text =  ptext_ar[$attribute][$lang] ;
             elseif( array_key_exists ( 'FR' ,  $ptext_ar[$attribute]))
                $text =  $text_ar[$attribute]["FR"] ;
             elseif( array_key_exists ( 'EN' ,  $ptext_ar[$attribute] ))
               $text =  $ptext_ar[$attribute]["EN"] ;
             else
                $text =$attribute." not found" ;
                
            return $text;
   }
    
}
