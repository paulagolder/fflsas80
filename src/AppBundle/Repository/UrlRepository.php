<?php

// src/Repository/UserRepository.php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UrlRepository extends EntityRepository 
{
   
    
    
     public function findAll()
    {
       $qb = $this->createQueryBuilder("p");
       $qb->orderBy("p.label", "ASC");
        $qb->orderBy("p.tags", "ASC");
       $urls =  $qb->getQuery()->getResult();
      
       return $urls;
    }
    
    
      public function findOne($urlid)
    {
        return $this->createQueryBuilder('u')
            ->where('u.id = :uid ')
            ->setParameter('uid', $urlid)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function delete($urlid)
    {
        $qd = $this->createQueryBuilder('u');
        $qd->delete();
        $qd->where('u.id = :uid');
        $qd->setParameter('uid',$urlid);
        $query = $qd->getQuery()->getResult();
    }
    
      public function findSearch($sfield)
    {
       $qb = $this->createQueryBuilder("u");
       $qb->andWhere('u.label LIKE :pid  or u.url LIKE :pid ');
       $qb->setParameter('pid', $sfield);
       $qb->orderBy("u.label", "ASC");
       $urls =  $qb->getQuery()->getResult();
      
       return $urls;
    }
}
