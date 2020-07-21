<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IncidentTypeRepository")
 * @ORM\Table(name="incidenttype")
 */
class IncidentType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $itypeid;
    
    /**
     * @ORM\Column(type="string", length=30)
     */
    private $label;

    public function getId()
    {
        return $this->id;
    }

    public function getItypeid(): ?int
    {
        return $this->itypeid;
    }

    public function setItypeid(int $itypeid): self
    {
        $this->itypeid = $itypeid;

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
}
