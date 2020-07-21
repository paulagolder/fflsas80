<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
##use Doctrine\Bundle\DoctrineBundle\Repository\EntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use AppBundle\Entity\Text;


class TextRepository extends EntityRepository
{
 
 
    public function findAll()
    {
       $qb = $this->createQueryBuilder("t");
       $qb->orderBy("t.id", "ASC");
       $texts =  $qb->getQuery()->getResult();
       
       foreach( $texts as $text)
       {
          $text->label = $text->getObjecttype().":".$text->getObjid().":".$text->getAttribute().":".$text->getLanguage();
       }
       return $texts;
       
    }
    
  
    public function findGroup($objecttype, $objid)
    {
        $texts =  $this->createQueryBuilder('t')
            ->andWhere('t.objecttype = :ot')
            ->andWhere('t.objid = :oid')
            ->setParameter('ot', $objecttype)
            ->setParameter('oid', $objid)
            ->getQuery()
            ->getResult();
       $text_ar= array();
        foreach( $texts as $text)
       {
          $url = "/".$objecttype."/".$objid;
          $language = $text->getLanguage();
          $attribute = $text->getAttribute();
          $comment = $text->getComment();
          $text_ar[$attribute][$language]['id'] = $text->getId();
          $text_ar[$attribute][$language]['link'] = $url;
          $text_ar[$attribute][$language]['objid'] = $objid;
          $text_ar[$attribute][$language]['objecttype'] = $objecttype;
          $text_ar[$attribute][$language]['comment'] = $comment;
          $text_ar[$attribute][$language]['label'] = $objecttype.":".$objid;
       }
        #echo("=+=+=".$text_ar['link']);
       return $text_ar;
    }
    
     public function findGroup2($objecttype, $objid)
    {
        $texts =  $this->createQueryBuilder('t')
            ->andWhere('t.objecttype = :ot')
            ->andWhere('t.objid = :oid')
            ->setParameter('ot', $objecttype)
            ->setParameter('oid', $objid)
            ->getQuery()
            ->getResult();
       $text_ar= array();
       $k =0;
        foreach( $texts as $text)
       {
         
          $language = $text->getLanguage();
          $attribute = $text->getAttribute();
          $comment = $text->getComment();
          $url = "/".$objecttype."/".$objid."/".$attribute."/".$language;
          $text_ar[$k]['id'] = $text->getId();
          $text_ar[$k]['link'] = $url;
          $text_ar[$k]['objid'] = $objid;
          $text_ar[$k]['objecttype'] = $objecttype;
          $text_ar[$k]['attribute']= $attribute;
          $text_ar[$k]['language'] = $language;
          $text_ar[$k]['comment']=$comment;
          $text_ar[$k]['label'] = $objecttype.":".$objid;
          $k++;
       }
       return $text_ar;
    }
   
   
     public function findOnebyoal($objecttype, $objid,$attribute,$language)
    {
        $text =  $this->createQueryBuilder('t')
            ->andWhere('t.objecttype = :ot')
            ->andWhere('t.objid = :oid')
              ->andWhere('t.attribute = :att')
                ->andWhere('t.language= :lang')
            ->setParameter('ot', $objecttype)
            ->setParameter('oid', $objid)
             ->setParameter('lang', $language)
              ->setParameter('att', $attribute)
            ->getQuery()
            ->getOneOrNullResult();
         if($text)
         {
          $url = "/".$objecttype."/".$objid;

          $comment = $text->getComment();
          $text->link = $url;
         # $text->setobjid($objid);
         # $text->setobjecttype($objecttype);
         # $text->setattribute($attribute);
         # $text->setlanguage($language);
         # $text->getcomment($comment);
          $text->label = $objecttype.":".$objid.":".$attribute.":".$language;
         }
       return $text;
    }
    
       public function findOne($id)
    {
        $text =  $this->createQueryBuilder('t')
            ->andWhere('t.id= :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
            $objecttype = $text->getObjecttype();
            $objid = $text->getObjid();
            $attribute = $text->getAttribute();
            $language = $text->getLanguage();
         if($text)
         {
          $url = "/".$objecttype."/".$objid;
          $comment = $text->getComment();
          $text->link = $url;;
          $text->label = $objecttype.":".$objid.":".$attribute.":".$language;
         }
       return $text;
    }
   
    public function findTexts($searchfield)
    {
        $manager = $this->getEntityManager();
        $conn = $manager->getConnection();
        $texts = $conn->query('select * from text where comment LIKE "'.$searchfield.'"')->fetchAll();
        $text_ar= array();
        foreach( $texts as $text)
       {
          $objecttype = $text['objecttype'];
          $objid = $text['objid'];
          $url = "/".$objecttype."/".$objid;
          $language = $text['language'];
          $attribute = $text['attribute'];
          $comment = $text['comment'];
          $text_ar[$objecttype][$objid]['link'] = $url;
          $text_ar[$objecttype][$objid][$attribute][$language] = $comment;
          $text_ar[$objecttype][$objid]['label'] = $objecttype.":".$objid;
       }
       return $text_ar;
    }
   
    public function deleteTexts( $objecttype, $objid)
    {
        $sql = "delete FROM  AppBundle\Entity\Text p where  p.objid = ".$objid. " and p.objecttype = '".$objecttype."'";
        $query = $this->getEntityManager()->createQuery($sql);
        $numDeleted = $query->execute();
        return $numDeleted;
    }
   
}
