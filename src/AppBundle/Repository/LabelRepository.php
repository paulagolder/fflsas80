<?php

// src/Repository/UserRepository.php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class LabelRepository extends EntityRepository 
{
   
    
    
     public function findmode($mode)
    {
       $qb = $this->createQueryBuilder("l");
       $qb->andWhere('l.mode = :mode   ');
       $qb->setParameter('mode', $mode);
       $qb->orderBy("l.tag", "ASC");
       $labels =  $qb->getQuery()->getResult();
      
       return $labels;
    }
    
    
      public function findOne($lid)
    {
        return $this->createQueryBuilder('l')
            ->where('l.id = :lid ')
            ->setParameter('lid', $lid)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function delete($lid)
    {
        $qd = $this->createQueryBuilder('l');
        $qd->delete();
        $qd->where('l.id = :lid');
        $qd->setParameter('lid',$lid);
        $query = $qd->getQuery()->getResult();
    }
    
       public function deletebytag($tag,$mode)
    {
        $qd = $this->createQueryBuilder('l');
        $qd->delete();
        $qd->where('l.tag = :tag and l.mode = :mode ');
        $qd->setParameter('tag',$tag);
        $qd->setParameter('mode',$mode);
        $query = $qd->getQuery()->getResult();
    }
    
      public function findSearch($sfield,$mode)
    {
       $qb = $this->createQueryBuilder("l");
       $qb->andWhere('l.text LIKE :pid  or l.tag LIKE :pid ');
       $qb->andWhere('l.mode = :mode   ');
       $qb->setParameter('pid', $sfield);
       $qb->setParameter('mode', $mode);
       $qb->orderBy("l.tag", "ASC");
       $labels =  $qb->getQuery()->getResult();
       return $labels;
    }
}
