<?php

namespace AppBundle\Repository;

use AppBundle\Entity\location;

use Doctrine\ORM\EntityRepository;
#use Doctrine\Bundle\DoctrineBundle\Repository\EntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 
 */
class LocationRepository extends EntityRepository
{
    

      public function findAll()
    {
       $qb = $this->createQueryBuilder("l");
       $qb->orderBy('l.name', 'ASC');
       $locations =  $qb->getQuery()->getResult();
       return $locations;
       
    }
    
    
     public function findTop()
    {
       $qb = $this->createQueryBuilder("l");
       $qb->andWhere('l.region = 0');
       $location =  $qb->getQuery()->getOneOrNullResult();
       return $location;
    }
    
     
     public function findChildren($lid)
    {
       $qb = $this->createQueryBuilder("l");
       $qb->andWhere('l.region = :lid');
       $qb->setParameter('lid', $lid);
       $qb->orderBy('l.name', 'ASC');
       $locations =  $qb->getQuery()->getResult();
       foreach( $locations as $location)
       {
         # $url = "/location/".$location->getlocid();
          #$location->link = $url;
       }
       return $locations;
    
    }
    
     public function findSearch($sfield)
    {
       $qb = $this->createQueryBuilder("p");
       $qb->andWhere('p.name LIKE :pid  ');
       $qb->setParameter('pid', $sfield);
       $qb->orderBy("p.name", "ASC");
       $places =  $qb->getQuery()->getResult();
     
       return $places;
    }
    
    
    private function makefindonequery($lid)
    {
       $qb = $this->createQueryBuilder("l");
       $qb->andWhere('l.locid = :lid');
       $qb->setParameter('lid', $lid);
       $qb->orderBy('l.name', 'ASC');
       return $qb->getQuery();
    }
    
    public function findOne($locationid)
    {
       $qb = $this->makefindonequery($locationid);
       $location =  $qb->getOneOrNullResult();
       if($location == null) return $location;
       $e = $location->getRegion();
       $a=0;
       while($e)
       {
          $location->ancestors[$a]['id'] = $e;
          $location->ancestors[$a]['link'] = "/admin/location/".$e;
          $region = $this->makefindonequery($e)->getOneOrNullResult();
          $location->ancestors[$a]['name'] =$region->getName();
          $e = $region->getRegion();
          $a++;
       }
        $children = $this->findChildren($locationid);
        for($i=0;$i<count($children);$i++)
        {
          $child = $children[$i];
          $location->children[$i]['id']= $child->getlocid();
          $location->children[$i]['link']= "/admin/location/".$child->getRegion();
          $location->children[$i]['name']= $child->getName();
        }
       return $location;
    }
    
    
    
}
