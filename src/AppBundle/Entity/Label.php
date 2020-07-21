<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LabelRepository")
 * @ORM\Table(name="label")
 */
class Label
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $tag;
    
    /**
     * @ORM\Column(type="string", length=10)
     */
    private $lang;
    
    /**
     * @ORM\Column(type="string", length=20)
     */
    private $mode;
    
      /**
     * @ORM\Column(type="string", length=300)
     */
    private $text;
      
    
    public function setId(int $uid)
    {
        $this->id = $uid;

        return $this;
    }

       public function getId()
    {
        

        return $this->id;
    }

    public function getText():?string
    {
        return $this->text;
    }

    public function setText(string $text)
    {
        $this->text = $text;

        return $this;
    }
    
    
    
    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
    
     public function getLang(): ?string
    {
        return $this->lang;
    }

    public function setLang(string $lang): self
    {
        $this->lang = $lang;

        return $this;
    }
    
     public function getMode(): ?string
    {
        return $this->mode;
    }

    public function setMode(string $mode): self
    {
        $this->mode = $mode;

        return $this;
    }
}
