<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $eventid;

   

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $contributor;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $update_dt;

  

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $label;

    /**
     * @ORM\Column(type="smallint")
     */
    private $parent;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $locid;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $startdate;

    /**
     * @ORM\Column(type="string",  nullable=true)
     */
    private $enddate;

    
        /**
     * @ORM\Column(type="smallint", length=20, nullable=true)
     */
    private $sequence;
    
    public $link;
    public $ancestors = array();
    public $children = array();
    public $participantinfo ;
    public $location = array();

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

    public function getEventid(): ?int
    {
        return $this->eventid;
    }

    public function setEventid(int $eventid): self
    {
        $this->eventid = $eventid;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getParent(): ?int
    {
        return $this->parent;
    }

    public function setParent(?int $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getLocid(): ?int
    {
        return $this->locid;
    }

    public function setLocid(?int $locid): self
    {
        $this->locid = $locid;

        return $this;
    }

    public function getStartdate()
    {
        return $this->startdate;
    }

    public function setStartdate( $startdate): self
    {
        $this->startdate = $startdate;

        return $this;
    }

    public function getEnddate() 
    {
        return $this->enddate;
    }

    public function setEnddate($enddate): self
    {
        $this->enddate = $enddate;

        return $this;
    }
    
        public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function setSequence(?int $seq): self
    {
        $this->sequence = $seq;

        return $this;
    }
}
