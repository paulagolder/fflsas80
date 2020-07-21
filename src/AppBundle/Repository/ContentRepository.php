<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Content;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;



class ContentRepository extends EntityRepository
{

    public $em ;

    public function findAll()
    {
       $sql = "select c from AppBundle:content c ";
       $sql .= " order by c.title ASC ";
       $query = $this->getEntityManager()->createQuery($sql);
       $contents = $query->getResult();
       return $contents;
    }
    
    
    public function findNews()
    {
       $sql = "select c from AppBundle:content c ";
       $sql .= " where c.tags LIKE '%news%'  ";
       $sql .= " order by c.update_dt DESC ";
       //dump ($sql);
       $query = $this->getEntityManager()->createQuery($sql);
       $contents = $query->getResult();
      // dump($contents);
       return $contents;
    }
    public function findOne($contentid)
    {
       $sql = "select c from AppBundle:content c ";
       $sql .= " where c.contentid = ".$contentid." ";
       $query = $this->getEntityManager()->createQuery($sql);
       $contents = $query->getResult();
       if(sizeof($contents) == 0) return null;
       return $contents[0];
    }
    
    public function findMaxSid()
    {
       $sql = "select max(c.subjectid) from AppBundle:content c ";
       $query = $this->getEntityManager()->createQuery($sql);
       $sid = $query->getResult();
       return $sid;
    }
    
    public function findContentLang($subjectid,$lang)
    {
       $qb = $this->createQueryBuilder("i");
       $qb->andWhere('i.subjectid = :sid');
       $qb->setParameter('sid', $subjectid);
     
       $contents =  $qb->getQuery()->getResult();
       if(sizeof($contents) == 0) return null;
       if(sizeof($contents) == 1 ) return $contents[0];
       else
       {
        if( $contents[0]->getLanguage() == $lang)  return $contents[0];
        else return $contents[1];
       }
    }
    
    public function findSubject($subjectid)
    {
       $sql = "select c from AppBundle:content c ";
       $sql .= " where c.subjectid = '".$subjectid."' ";
       $query = $this->getEntityManager()->createQuery($sql);
       $contents = $query->getResult();
       $content_ar = array();
       foreach( $contents as $content )
       {
         $key = $content->getLanguage();
         $content_ar[$key] = $content;
       }
       return $content_ar;
    }
    
    
  
    
    
    public function findSearch($sfield)
    {
       $qb = $this->createQueryBuilder("p");
       $qb->andWhere('p.title LIKE :pid  or p.text LIKE :pid ');
       $qb->setParameter('pid', $sfield);
       $qb->orderBy("p.title", "ASC");
       $contents =  $qb->getQuery()->getResult();
      
       return $contents;
    }
    
     public function delete($contentid)
    {
        $sql = "delete FROM  AppBundle\Entity\Content c where c.contentid = ".$contentid ;
        $query = $this->getEntityManager()->createQuery($sql);
        $numDeleted = $query->execute();
        return $numDeleted;
    }
    
}
