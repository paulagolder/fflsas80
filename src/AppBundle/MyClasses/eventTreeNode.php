<?php

namespace AppBundle\MyClasses;
use AppBundle\Entity\event;
use AppBundle\Repository\TextsRepository;

class eventTreeNode
{

      private $eventid;
      private $children= array();
      private $label;
      private $link ;
      private $participantinfo;
      private $sequence ;
      

     
     function __construct( int $eid )
     {
        $this->eventid = $eid;
     }
     
     
    public function getEventid()
    {
        return $this->eventid;
    }

    public function setEventid(int $eid)
    {
        $this->eventid = $eid;
    }

    public function setLabel( string $text="**")
    {
        $this->label= $text;
    }

    public function setParticipantInfo($text="==")
    {
        $this->participantinfo = $text;
    }
    public function getLink()
    {
        return $this->link;
    }
    
    public function setSequence( $seq)
    {
        $this->sequence = $seq;
    }
    
       public function getSequence()
    {
        return $this->sequence;
    }
    
    public function setLink(string $url)
    {
        $this->link = $url;
    }

    public function getLabel()
    {
        return $this->label;
    }
    
      public function getParticipantInfo()
    {
        return $this->participantinfo;
    }
    
    
    public function addChild(eventTreeNode $newchild )
    {
      array_push( $this->children, $newchild);
    }
    
    public function getChildren()
    {
      return $this->children;
    }
    
     public function setChildren($carray)
    {
      $this->children = $carray;;
    }
    
    public function getChild(int $int)
    {
      return $this->children[$int];
    }
    
     public function clearChildren()
    {
      $children=array();
    }

    public function countChildren()
    {
      return count($children);
    }
    
    
    public function hasChild($eventid)
    {
       foreach($this->children as $child)
       {
         if($child->getEventid() == $eventid) return true;
       }
       return false;
    }
    
    public function findChild($eventid): self
    {
       foreach($this->children as $child)
       {
         if($child->getEventid() == $eventid) return $child;
       }
       return Null;
    }
    
    public function sortChildren()
    {
       $uschildren = $this->children;
       usort($uschildren , array($this, "cmp_obj"));
       $this->setChildren($uschildren);
    }
    
    
     public function sortsubtree()
    {
       $uschildren = $this->children;
       foreach($uschildren as $child)
       {
          $child->sortSubtree();
       }
       $this->sortChildren();
    }
    
    static function cmp_obj($a, $b)
    {
       
        if ($a->sequence == $b->sequence) {
            return 0;
        }
        return ($a->sequence > $b->sequence) ? +1 : -1;
    }
}
