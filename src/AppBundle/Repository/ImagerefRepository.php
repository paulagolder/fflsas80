<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Imageref;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Driver\Connection;


class ImagerefRepository extends EntityRepository
{

    public function findOne($refid)
    {
        $ref =  $this->createQueryBuilder('t')
            ->andWhere('t.id = :refid')
            ->setParameter('refid', $refid)
            ->getQuery()
            ->getOneOrNullResult();
       return $ref;
    }
 
    public function findMatch($objecttype, $objid,$imageid)
    {
        $ref =  $this->createQueryBuilder('t')
            ->andWhere('t.imageid = :imageid')
            ->setParameter('imageid', $imageid)
            ->andWhere('t.objecttype = :ot')
            ->andWhere('t.objid = :oid')
            ->setParameter('ot', $objecttype)
            ->setParameter('oid', $objid)
            ->getQuery()
            ->getOneOrNullResult();
       return $ref;
    }

    public function findGroup($objecttype, $objid)
    {
      $sql = "select t from AppBundle:imageref t ";
      $sql .= " where t.objecttype = '".$objecttype."' ";
      $sql .= " and t.objid = ".$objid;
      $query = $this->getEntityManager()->createQuery($sql);
        $refs = $query->getResult();
            
                 
       $ref_ar= array();
       $i=0;
        foreach( $refs as $ref)
       {
          $url = "/".$objecttype."/".$objid;
          $ref_ar[$i]['id'] = $ref->getId();
          $ref_ar[$i]['link'] = $url;
          $ref_ar[$i]['imageid'] = $ref->getimageid();
          $ref_ar[$i]['label'] =  $ref->getobjecttype().":".  $ref->getobjid();
          $i++;
       }
       return $ref_ar;
    }
    
     public function findAllGroups( $imgid)
     {
      $sql = "select t from AppBundle:imageref t ";
      $sql .= " where t.imageid= ".$imgid." ";
      $query = $this->getEntityManager()->createQuery($sql);
        $refs = $query->getResult();
  
       $ref_ar= array();

        foreach( $refs as $ref)
       {
          $objecttype = $ref->getobjecttype();
          $objid = $ref->getobjid();
          $ref_ar[$objecttype][$objid]['link'] = "/".$objecttype."/". $objid;
          $ref_ar[$objecttype][$objid]['imageid'] = $ref->getimageid();
       }
       return $ref_ar;
    }
    
    public function delete($objecttype, $objid, $imageid)
    {
        $sql = "delete FROM  AppBundle\Entity\Imageref p where p.objecttype = '".$objecttype."'";
        $sql .= ' and p.objid = '.$objid;
        $sql .= ' and p.imageid = '.$imageid;
        $query = $this->getEntityManager()->createQuery($sql);
        $numDeleted = $query->execute();
        return $numDeleted;
    }
    
      public function deleteGroup($objecttype, $objid)
    {
        $sql = "delete FROM  AppBundle\Entity\Imageref p where p.objecttype = '".$objecttype."'";
        $sql .= ' and p.objid = '.$objid;
        $query = $this->getEntityManager()->createQuery($sql);
        $numDeleted = $query->execute();
        return $numDeleted;
    }
    
    public function deleteAllImages( $imageid)
    {
        $sql = "delete FROM  AppBundle\Entity\Imageref p where  p.imageid = ".$imageid;
        $query = $this->getEntityManager()->createQuery($sql);
        $numDeleted = $query->execute();
        return $numDeleted;
    }
    
}
