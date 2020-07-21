<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonRepository")
 */
class Person
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $personid;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $forename;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $alias;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $contributor;

    /** @ORM\Column(type="datetime") */
     
    private $update_dt;
    
    
    private $fullname;
    private $label;
    public $link;
    

    public function getId()
    {
        return $this->personid;
    }

    public function getPersonid(): ?int
    {
        return $this->personid;
    }

    public function setPersonid(int $personid): self
    {
        $this->personid = $personid;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getForename(): ?string
    {
        return $this->forename;
    }

    public function setForename(?string $forename): self
    {
        $this->forename = $forename;

        return $this;
    }
    
     public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(?string $name): self
    {
        $this->fullname = $name;

        return $this;
    }

      public function getLabel(): ?string
    {
        return $this->fullname;
    }

    public function setLabel(?string $name): self
    {
        $this->fullname = $name;

        return $this;
    }
    
    
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

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
    
    public function fixperson(): self
    {
         $fname = $this->getSurname();
        if($this->getForename() )
        {
          $fname .= ", ".$this->getForename();
        }
        if($this->getAlias() )
        {
           $fname .= " (".$this->getAlias().")";
        }
     ##   $url = "/".$this->lang."/person/".$this->getPersonid();
        $this->link = '';
        $this->fullname = $fname;

        return $this;
    }
    
    
    private function makefullname($person): string
    {
         $fname = $person->getSurname();
        if($person->getForename() )
        {
          $fname .= ", ".$person->getForename();
        }
       
        return $fname;

    }
    
}
