<?php

namespace AppBundle\Repository;

use AppBundle\Entity\incident;
use AppBundle\Entity\incidenttype;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
#use Doctrine\Bundle\DoctrineBundle\Repository\EntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class IncidentRepository extends EntityRepository
{
    
    
    public function findAll()
    {
        
        $qb = $this->createQueryBuilder("i");
        $qb->select('i.incidentid','i.personid','i.eventid','i.itypeid','i.name_recorded', 'i.sdate', 'i.edate', 'i.locid','i.comment','pl.surname','pl.forename' , 'it.label as typename');
        $qb->join(' AppBundle\Entity\Person', 'pl', 'WITH', 'pl.personid = i.personid');
        $qb->join(' AppBundle\Entity\IncidentType', 'it', 'WITH', 'it.itypeid = i.itypeid');
        $qb->orderBy('pl.surname', 'ASC');
        $qbq = $qb->getQuery();
        $incidents =  $qbq->getResult();
        foreach( $incidents as $key=>$incident)
        {
            $url = "/incident/".$incident['incidentid'];
            $incidents[$key]['link'] = $url;
            $incidents[$key]['label'] = $incident['surname'].", ".$incident['forename'];
            #echo( $incident['surname'].", ".$incident['forename']);
        }
        return $incidents;
        
    }
    
    public function seekByPerson($personid)
    {
        $sql = "select i.incidentid,i.personid,i.eventid,i.itypeid,i.name_recorded, i.sdate, i.edate, i.locid,i.comment,it.label from AppBundle:Incident i ";
        $sql .= " join AppBundle\Entity\IncidentType it with it.itypeid = i.itypeid ";
        $sql .= " where i.personid = ".$personid." ";
        $sql .= " order by i.sdate ASC  ";
        $query = $this->getEntityManager()->createQuery($sql);
        $incidents = $query->getResult();
     
        $incid_ar = array();
        foreach( $incidents as $incident)
        {
            $url = "/incident/".$incident['incidentid'];
            $incident['link'] = $url;
            $incid_ar[] = $incident;
        }
        return $incid_ar;
        
    }
    
    public function findbyParticipation($eventid,$personid)
    {
        $qb = $this->createQueryBuilder("i");
        $qb->select('i.incidentid','i.personid','i.eventid','i.itypeid','i.name_recorded', 'i.sdate', 'i.edate', 'i.locid','i.comment','it.label' ,'i.sequence');
        $qb->join(' AppBundle\Entity\IncidentType', 'it', 'WITH', 'it.itypeid = i.itypeid');
        $qb->andWhere('i.personid = :pid');
        $qb->setParameter('pid', $personid);
        $qb->andWhere('i.eventid = :eid');
        $qb->setParameter('eid', $eventid);
        $qb->orderBy('i.sdate', 'ASC');
        $qbq = $qb->getQuery();
        $incidents =  $qbq->getResult();
        $incid_ar = array();
        foreach( $incidents as $incident)
        {
            $url = "/admin/incident/".$incident['incidentid'];
            $incident['link'] = $url;
            $incid_ar[] = $incident;
        }
        return $incid_ar;
    }
    
    
    public function findOne($incidentid)
    {
        
         $qb = $this->createQueryBuilder("i");
        $qb->andWhere(" i.incidentid = :iid ");
          $qb->setParameter('iid', $incidentid);
         $qbq = $qb->getQuery();
        $incidents =  $qbq->getResult();
         
       return $incidents[0];
    }
    
    public function findLocations($locid)
    {
        $sql = "select i from AppBundle:incident i ";
        $sql .= " where i.locid = ".$locid." ";
        $sql .= " order by i.sdate ASC  ";
        $query = $this->getEntityManager()->createQuery($sql);
        $incidents = $query->getResult();
        
        
        return $incidents;
    }
    
    
    public function delete($incidentid)
    {
        
        $qd = $this->createQueryBuilder('i');
        $qd->delete();
        $qd->where('i.incidentid = :uid');
        $qd->setParameter('uid',$incidentid);
        $query = $qd->getQuery()->getResult();
    }
    
    
}
