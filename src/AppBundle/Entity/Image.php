<?php

// src/entity/image.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageRepository")
 */
class Image
{
    

    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $imageid;
    
    
      /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;
    
    
    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $path;
    
 
      /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $format;
   
      /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $copyright;
  
  /**
     * @ORM\Column(type="integer", nullable=true)
     *   'Public' => 0,
     *   'Admin' => 1,
      *  'Private' => 2,   
     */
  private $access;
    
    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $contributor;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $update_dt;
    
    public $link;
    public $fullpath;
    private $label;
     /**
     * @ORM\Column(type="string")
     *
     *
     */
    private $Imagefile;


    public function getImageid(): ?int
    {
        return $this->imageid;
    }

    public function setImageid(int $imageid): self
    {
        $this->imageid = $imageid;

        return $this;
    }

      public function getName(): ?string
    {
        return $this->name;
    }

  
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

  
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }
    
      public function getImagefile()
    {
        return $this->Imagefile;
    }

  
    public function setImagefile($path): self
    {
        $this->Imagefile = $path;

        return $this;
    }
 
   
    
    
      public function getFullpath()
    {
        return $this->fullpath;
    }


    
    public function setCopyright($copyr)
    {
        $this->copyright = $copyr;

        return $this;
    }
    
         public function getCopyright()
    {
        return $this->copyright;
    }


    
    public function setFullpath($file)
    {
        $this->fullpath = $file;

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

 
    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): self
    {
        $this->format = $format;

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

    public function getAccess(): ?int
    {
        return $this->access;
    }

    public function setAccess(?int $access): self
    {
        $this->access = $access;

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
    
    public function makeLabel()
    {
       if($this->name == null)
      {
          $k =strrpos ( $this->path, "/" );
          $j =strrpos ( $this->path, "." );
          $label = substr($this->path, $k+1,$j-$k-1);
            $this->label = $label;
       }
       else
       {  
         $this->label = $this->name;
       }
    }
    
     public function makeFilename()
    {
       $filename="";
       if($this->name == null)
       {
         $filename="no_name";
       }
       else
       {  
         $filename= trim($this->name);
       }
       $filename = str_replace (' ' , '_' , $filename);  
       $filename = str_replace ('__' , '_' , $filename);  
       $filename = preg_replace( '/[\W]/', '', $filename);
       $filename = $filename."_".$this->imageid;
       return $filename;
    }
    
     public function isTemp()
     {
       $startstr = substr($this->path,0,4);
       $year =  (int)$startstr;
       if($year > "2000")
       {
         return true;
       }
       else
       {
        return false;
       }
     }
       
       
     
}
