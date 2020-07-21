<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Event;
use Doctrine\ORM\EntityRepository;
#use Doctrine\Bundle\DoctrineBundle\Repository\EntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;



class EventRepository extends EntityRepository
{
   # public function __construct(RegistryInterface $registry)
    #{
   #     parent::__construct($registry, event::class);
    #}


    
    
    public function findAll()
    {
       $qb = $this->createQueryBuilder("e");
       $qb->orderBy('e.startdate');
       $qb->orderBy('e.sequence');
       $events =  $qb->getQuery()->getResult();
       
       foreach( $events as $event)
       {
         # $url = "/event/".$event->getEventid();
         # $event->link = $url;
       }
       return $events;
       
    }
    
    
    private function findChildren($eid)
    {
       $qb = $this->createQueryBuilder("e");
       $qb->andWhere('e.parent = :eid');
       $qb->orderBy('e.startdate');
       $qb->addorderBy('e.sequence');
       $qb->setParameter('eid', $eid);
       $events =  $qb->getQuery()->getResult();
       return $events;
    
    }
    
    public function findTop()
    {
       $qb = $this->createQueryBuilder("e");
       $qb->andWhere('e.parent = 0');
       $event =  $qb->getQuery()->getOneOrNullResult();
       return $event;
    }
    
    
    private function makefindonequery($eid)
    {
       $qb = $this->createQueryBuilder("p");
       $qb->andWhere('p.eventid = :eid');
       $qb->setParameter('eid', $eid);
       return $qb->getQuery();
    }
    
    public function findOne($eventid)
    {
       $qb = $this->makefindonequery($eventid);
       $event =  $qb->getOneOrNullResult();
       if($event == null) return null;
       $e = $event->getParent();
       $a=0;
       $event->title = $event->getLabel();
       while($e)
       {
          $parent = $this->makefindonequery($e)->getOneOrNullResult();
          $event->ancestors[$a] = $parent;
          $e = $parent->getParent();
          $a++;
       }
                
       $children = $this->findChildren($eventid);
       for($i=0;$i<count($children);$i++)
       {
          $child = $children[$i];
          $event->children[$i]['id']= $child->getEventid();
          $event->children[$i]['event']= $child;
       }
       return $event;
    }
    

 

    
    
    public function findLocations($locid)
    {
       $qb = $this->createQueryBuilder("p");
       $qb->andWhere('p.locid = :lid');
       $qb->setParameter('lid', $locid);
      
       $events =   $qb->getQuery()->getResult();
       return $events;
    }
    
       public function findLatest($max)
    {
       $platest = array();
       $qb = $this->createQueryBuilder("p");
       $qb->orderBy("p.update_dt", "DESC");
       $qb->setMaxResults($max);
       $events =  $qb->getQuery()->getResult();
       $n =0;
       foreach( $events as $event)
       {
          $platest[$n]["objecttype"]="event";
          $platest[$n]["objid"]=$event->getEventId();
          $platest[$n]["label"]=$event->getLabel();
          $platest[$n]["date"]=$event->getUpdateDt();
          $platest[$n]["link"]="event/".$event->getEventId();
          $n++;
       }
       return $platest;
    }
    
}
