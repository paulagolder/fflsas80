<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BibloRepository")
 * @ORM\Table(name="biblo")
 */
class Biblo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $bookid;

    /**
     * @ORM\Column(type="string", length=300)
     */
    private $title;
    
        /**
     * @ORM\Column(type="string", length=300)
     */
    private $subtitle;
    
    /**
     * @ORM\Column(type="string", length=300)
     */
    private $author;
    
    /**
     * @ORM\Column(type="string", length=300)
     */
    private $publisher;
    
        /**
     * @ORM\Column(type="string", length=300)
     */
    private $year;
    
    
        /**
     * @ORM\Column(type="string", length=300)
     */
    private $isbn;
    
         /**
     * @ORM\Column(type="string", length=300)
     */
    private $tags;
 

    public function getBookId(): ?int
    {
        return $this->bookid;
    }
    
    public function setBookId(int $uid)
    {
        $this->bookid = $uid;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }
    
    public function getLabel(): ?string
    {
        return $this->title;
    }


    public function setSubtitle(string $title): self
    {
        $this->subtitle = $title;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
    
    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(string $pub): self
    {
        $this->publisher = $pub;

        return $this;
    }
    
      public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }
    
      public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }
    
    
      public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }
    
       public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }
}
