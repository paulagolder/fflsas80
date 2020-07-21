<?php

namespace AppBundle\MyClasses;
use AppBundle\Entity\event;
use AppBundle\MyClasses\eventTreeNode;
use AppBundle\Repository\TextsRepository;

class eventTree
{
      private $topNode;
     
     function __construct( Event $ev) 
     {
        $this->topNode = new eventTreeNode($ev->getEventid());
        $this->topNode->setLabel($ev->getLabel());
       
     }
     
     
    function buildTree( array $events)
    {
         $topid = $this->topNode->getEventid();
         foreach ($events as $event)
         {
         if($event->getEventid()== $topid)
         {
           var_dump("not adding ". $event->getEventid());
         }
         else
         {
           $this->addAncestors($event);
         }
         }
    }
    
    public function getTopNode()
    {
       return $this->topNode;
    }
    
    
    
     function addAncestors(Event $ev)
     {
         $count = 0;
         $currentnode = $this->topNode;
         $parents = $ev->ancestors;
         $parentsr = array_reverse($parents);
          
         foreach($parentsr as $key=>$parent)
         {
             $peid = $parentsr[$key]->getEventid();
             $pevent = $parent;
             // echo ("=====".  $pevent->title);
             if($peid == $this->topNode->getEventid() )
             {
                 
             }
             else  if($currentnode->hasChild($peid))
             {
                 $currentnode = $currentnode->findChild($peid);
             }
             else 
             {
                 //addchild to current node
                 $newchild = new eventTreeNode($peid);
                 $newchild->setLabel($pevent->title);
                 $newchild->setLink($pevent->link);
                 $seq =$pevent->getSequence();
                 $sd = $pevent->getStartdate();
                 if( $sd > 19390000 ) $seq = $sd;
                  $newchild->setSequence($seq);
                $currentnode->addChild($newchild);
                 $cnseq = $currentnode->getSequence();
                if($cnseq > 1939000 && $seq > 1939000 && $seq <  $cnseq) $currentnode->setSequence($seq);
                elseif($cnseq < 1939000 && $seq > 1939000 ) $currentnode->setSequence($seq);
                 $currentnode = $newchild;
                 $count++;
             }
         }
         $newchild = new eventTreeNode($ev->getEventId());
         $newchild->setLabel($ev->title);
         $newchild->setLink($ev->link);
         $newchild->setParticipantInfo($ev->participantinfo);
         $seq =$ev->getSequence();
         $sd = $ev->getStartdate();
          if($seq < 19390000 && $sd > 19390000 ) $seq = $sd;
         elseif($seq > 19390000 && $sd < $seq && $sd > 19390000 ) $seq = $sd;
          $newchild->setSequence($seq);
         $currentnode->addChild($newchild);
         $cnseq = $currentnode->getSequence();
         if($cnseq > 1939000 && $seq > 1939000 && $seq <  $cnseq) $currentnode->setSequence($seq);
         elseif($cnseq < 1939000 && $seq > 1939000 ) $currentnode->setSequence($seq);
         return $count;
     }
    
    public function sortTree()
    {
       $this->topNode->sortsubtree();
    }
}
