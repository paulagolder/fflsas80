<?php

// src/Repository/UserRepository.php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BibloRepository extends EntityRepository 
{
   
    
    
     public function findAll()
    {
       $qb = $this->createQueryBuilder("p");
       $qb->orderBy("p.title", "ASC");
       $furls =  $qb->getQuery()->getResult();
      
       return $furls;
    }
    
    
      public function findOne($bookid)
    {
        return $this->createQueryBuilder('u')
            ->where('u.bookid = :bid ')
            ->setParameter('bid', $bookid)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function delete($bookid)
    {
        $qd = $this->createQueryBuilder('u');
        $qd->delete();
        $qd->where('u.bookid = :bid');
        $qd->setParameter('bid',$bookid);
        $query = $qd->getQuery()->getResult();
    }
    
      public function findSearch($sfield)
    {
       $qb = $this->createQueryBuilder("u");
       $qb->andWhere('u.title LIKE :pid  or u.author LIKE :pid or u.subtitle LIKE :pid ');
       $qb->setParameter('pid', $sfield);
       $qb->orderBy("u.title", "ASC");
       $urls =  $qb->getQuery()->getResult();
      
       return $urls;
    }
}
