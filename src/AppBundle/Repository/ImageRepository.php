<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Image;

use Doctrine\ORM\EntityRepository;
#use Doctrine\Bundle\DoctrineBundle\Repository\EntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;


class ImageRepository extends EntityRepository
{

   
    
    #public function __construct(RegistryInterface $registry)
    #{
    #    parent::__construct($registry, Image::class);
    #}


     public function findAll()
    {
       $qb = $this->createQueryBuilder("i");
       $qb->orderBy("i.name", "ASC");
       $images =  $qb->getQuery()->getResult();
       foreach($images as $image)
       {
         $image->makeLabel();
         
         //   $image->setFullpath($this->getParameter('new-images-folder').$this->getPath());
       }
       return $images;
    }
    
    
      public function findAllPublic()
    {
       $qb = $this->createQueryBuilder("i");
        $qb->where('i.access < 1 ');
       $qb->orderBy("i.name", "ASC");
       $images =  $qb->getQuery()->getResult();
       foreach($images as $image)
       {
         $image->makeLabel();
         
         //   $image->setFullpath($this->getParameter('new-images-folder').$this->getPath());
       }
       return $images;
    }
    
    public function findOne($imageid)
    {
       $qb = $this->createQueryBuilder("i");
       $qb->andWhere('i.imageid = :iid');
       $qb->setParameter('iid', $imageid);
       $image =  $qb->getQuery()->getOneOrNullResult();
       if($image)
       {
       $image->makeLabel();
 
       #$image->makeFullpath();
     #   echo("===".$image->getFullpath());
        }
       return $image;
    }
    
      public function findSearch($sfield)
    {
       $qb = $this->createQueryBuilder("p");
       $qb->andWhere('p.path LIKE :pid  or p.name LIKE :pid ');
       $qb->setParameter('pid', $sfield);
       $qb->orderBy("p.name", "ASC");
       $images =  $qb->getQuery()->getResult();
       foreach($images as $image)
       {
    #     $image->makeLabel();
    #     $image->makeFullpath();
       }
       return $images;
    }
    
    
    public function delete( $imageid)
    {
        $sql = "delete FROM  AppBundle\Entity\Image p where  p.imageid = ".$imageid;
        $query = $this->getEntityManager()->createQuery($sql);
        $numDeleted = $query->execute();
        return $numDeleted;
    }
    
     public function findLatest($max, $sec)
    {
       $platest = array();
       $qb = $this->createQueryBuilder("p");
       $qb->where ('p.access <= :sec ' );
       $qb->setParameter('sec', $sec);
       $qb->orderBy("p.update_dt", "DESC");
       $qb->setMaxResults($max);
       $images =  $qb->getQuery()->getResult();
       $n =0;
       foreach( $images as $image)
       {
          $platest[$n]["objecttype"]="image";
          $platest[$n]["objid"]=$image->getImageId();
          $platest[$n]["label"]=$image->getName();
          $platest[$n]["date"]=$image->getUpdateDt();
          $platest[$n]["link"]="image/".$image->getImageId();
          $n++;
       }
       return $platest;
    }
    
     public function findNew()
    {
       
       $qb = $this->createQueryBuilder("p");
       $qb->where ('p.path LIKE :new ' );
       $qb->setParameter('new', "201%");
       $qb->orderBy("p.update_dt", "DESC");

       $images =  $qb->getQuery()->getResult();
       
       return $images;
    }
}
