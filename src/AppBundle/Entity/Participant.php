<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParticipantRepository")
 */
class Participant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $participationid;

    /**
     * @ORM\Column(type="smallint")
     */
    private $personid;

    /**
     * @ORM\Column(type="smallint")
     */
    private $eventid;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $name_recorded;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $rank;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=14, nullable=true)
     */
    private $sdate;

    /**
     * @ORM\Column(type="string", length=14, nullable=true)
     */
    private $edate;

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

    public function getParticipationid(): ?int
    {
        return $this->participationid;
    }

    public function setParticipationid(int $participationid): self
    {
        $this->participationid = $participationid;

        return $this;
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

    public function getEventid(): ?int
    {
        return $this->eventid;
    }

    public function setEventid(int $eventid): self
    {
        $this->eventid = $eventid;

        return $this;
    }

    public function getNameRecorded(): ?string
    {
        return $this->name_recorded;
    }

    public function setNameRecorded(?string $name_recorded): self
    {
        $this->name_recorded = $name_recorded;

        return $this;
    }

    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setRank(?string $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getSdate(): ?string
    {
        return $this->sdate;
    }

    public function setSdate(?string $sdate): self
    {
        $this->sdate = $sdate;

        return $this;
    }

    public function getEdate(): ?string
    {
        return $this->edate;
    }

    public function setEdate(?string $edate): self
    {
        $this->edate = $edate;

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
