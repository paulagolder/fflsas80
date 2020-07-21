<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContentRepository")
 */
class Content
{
    
   
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $contentid;
    
    /**
     * @ORM\Column(type="string")
     */
    private $subjectid;
      /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;
    

   
    
        /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;
    
      /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $language;
      
    /**
     * @ORM\Column(type="integer", nullable=true)
     *   'Public' => 0,
     *   'Admin' => 1,
     *   'Private' => 2,  
     */
    private $access;
    
        /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $tags;
    
    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $contributor;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $update_dt;
    

    private $label;



    public function getContentid(): ?int
    {
        return $this->contentid;
    }

    public function setContentid(int $contentid): self
    {
        $this->contentid = $contentid;

        return $this;
    }
    
    
    
    
     public function getSubjectid(): ?string
    {
        return $this->subjectid;
    }

    public function setSubjectid($subjectid): self
    {
        $this->subjectid = $subjectid;

        return $this;
    }

     public function getAccess(): ?int
    {
        return $this->access;
    }

    public function setAccess(int $int): self
    {
        $this->access = $int;

        return $this;
    }
    
      public function getLabel(): ?string
    {
        return $this->title;
    }

  
    public function setLabel(string $name): self
    {
        $this->title = $name;

        return $this;
    }
    
    public function getTitle(): ?string
    {
        return $this->title;
    }

  
    public function setTitle(string $name): self
    {
        $this->title = $name;

        return $this;
    }
    

    public function getText(): ?string
    {
        return $this->text;
    }

  
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
    
    
     public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $lang): self
    {
        $this->language = $lang;

        return $this;
    }

    
      public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $text): self
    {
        $this->tags = $text;

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
