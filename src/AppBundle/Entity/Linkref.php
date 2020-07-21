<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LinkrefRepository")
 */
class Linkref
{
     /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $linkid;

  /**
     * @ORM\Column(type="string", length=100)
     */
    private $objecttype;

  /**
     * @ORM\Column(type="integer")
     */
    private $objid;
    
     /**
     * @ORM\Column(type="string", length=100)
     */
    
      private $label;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $path;

   /**
     * @ORM\Column(type="string", length=100)
     */
    private $doctype;

   
    private $sequence;

    
    private $contributor;

   
    private $update_dt;

   

    public function getLinkid(): ?int
    {
        return $this->linkid;
    }

    public function setLinkid(int $linkid): self
    {
        $this->linkid = $linkid;

        return $this;
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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    
     public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }
    
    public function getDoctype(): ?string
    {
        return $this->doctype;
    }

    public function setDoctype(?string $doctype): self
    {
        $this->doctype = $doctype;

        return $this;
    }

    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function setSequence(?int $sequence): self
    {
        $this->sequence = $sequence;

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
}
