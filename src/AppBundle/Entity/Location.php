<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LocationRepository")
 */
class Location
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
   
    private $locid;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $name;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $region;

    /**
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $kml;
    
     /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $zoom;
    
      /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showchildren = 0;
    
    public $link;
    public $label;
    public $ancestors = array();
    public $children = array();

    public function getId()
    {
        return $this->id;
    }

    public function getLocid(): ?int
    {
        return $this->locid;
    }

    public function setLocid(int $locid): self
    {
        $this->locid = $locid;

        return $this;
    }

    
    public function getZoom(): ?int
    {
     if ($this->zoom > 0)
        return $this->zoom;
      else
         return 1;
    }

    public function setZoom( $zoom): self
    {
     if ( is_int ( $zoom ))
      {
        $this->zoom = $zoom;
      }
     else
     {
        $this->zoom = 1;
      }
        return $this;
    }
    
    public function getShowchildren() :  ?bool
    {
        return $this->showchildren;
    }

    public function setShowchildren(?bool $show): self
    {
        $this->showchildren = $show;

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

    public function getRegion(): ?int
    {
        return $this->region;
    }

    public function setRegion(?int $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getKml(): ?string
    {
        return $this->kml;
    }

    public function setKml(?string $kml): self
    {
        $this->kml = $kml;

        return $this;
    }
    
      public function getLabel(): ?string
    {
        return $this->name;
    }

    public function setLabel(?string $label): self
    {
        $this->name = $label;

        return $this;
    }
    
       public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }
    
    
    public function getJson()
    {
      $str ="{";
      $str .=  '"locid":'.$this->locid.',';
      $str .=  '"name":"'.$this->name.'",';
      $str .=  '"label":"'.$this->label.'",';
      $str .=  '"link":"'.$this->link.'",';
      $str .=  '"kml":"'.$this->kml.'",';
      $str .=  '"longitude":'.$this->longitude.",";
      $str .=  '"latitude":'.$this->latitude.", ";
      if($this->showchildren)
      $str .=  '"showchildren":true ,';
      else
        $str .=  '"showchildren":false ,';
            $str .=  '"zoom":'.$this->getZoom()." ";
         if($this->children)
         {
           $childlist =', "children" : [';
           $first = true;
           foreach ($this->children as $child)
           {
             if($first )
             {
              $childlist .=  $child->getJsonShallow();
               $first = false;
              }
              else
              {
                 $childlist .= " , ".$child->getJsonShallow();
              }
            }
            $childlist .="]";
            $str .= $childlist;
         }
      $str .="}";
    return $str;
    }
    
     public function getJsonShallow()
    {
      $str ="{";
      $str .=  '"locid":'.$this->locid.',';
      $str .=  '"name":"'.$this->name.'",';
      $str .=  '"label":"'.$this->label.'",';
      $str .=  '"link":"'.$this->link.'",';
      $str .=  '"kml":"'.$this->kml.'",';
      $str .=  '"longitude":'.$this->longitude.",";
      $str .=  '"latitude":'.$this->latitude." ";
      $str .="}";
    return $str;
    }
    
}
