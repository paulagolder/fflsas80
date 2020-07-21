<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImagerefRepository")
 */
class Imageref
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $imageid;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $objecttype;

    /**
     * @ORM\Column(type="smallint")
     */
    private $objid;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $mustshow;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $width;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $sequence;

    public function getId()
    {
        return $this->id;
    }

    public function getImageid(): ?int
    {
        return $this->imageid;
    }

    public function setImageid(int $imageid): self
    {
        $this->imageid = $imageid;

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

    public function getMustshow(): ?string
    {
        return $this->mustshow;
    }

    public function setMustshow(?string $mustshow): self
    {
        $this->mustshow = $mustshow;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

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
}
