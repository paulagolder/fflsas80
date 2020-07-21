<?php

// src/Repository/UserRepository.php
namespace AppBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
     public function isuniqueName($username)
    {
        $users = $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getResult();
            if(count($users)>0) return false;
            else return true;
    }
    
      public function loadUserByEmail($email)
    {
        return $this->createQueryBuilder('u')
            ->where(' u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    
     public function findAll()
    {
       $qb = $this->createQueryBuilder("p");
       $qb->orderBy("p.username", "ASC");
       $fusers =  $qb->getQuery()->getResult();
      
       return $fusers;
    }
    
    
      public function findOne($userid)
    {
        return $this->createQueryBuilder('u')
            ->where('u.id = :uid ')
            ->setParameter('uid', $userid)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function delete($userid)
    {
        $qd = $this->createQueryBuilder('u');
        $qd->delete();
        $qd->where('u.id = :uid');
        $qd->setParameter('uid',$userid);
        $query = $qd->getQuery()->getResult();
    }
    
      public function findSearch($sfield)
    {
       $qb = $this->createQueryBuilder("u");
       $qb->andWhere('u.username LIKE :pid  or u.email LIKE :pid or u.rolestr LIKE :pid ');
       $qb->setParameter('pid', $sfield);
       $qb->orderBy("u.username", "ASC");
       $users =  $qb->getQuery()->getResult();
      
       return $users;
    }
}
