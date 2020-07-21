<?php

// src/Controller/RecentEditsController.php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use  AppBundle\Controller\Security;
use AppBundle\Entity\Image;
use AppBundle\Service\MyLibrary;

class RecentEditsController extends Controller
{
    private $lang="fr";
    private $mylib;
    private $requestStack ;
    
    public function __construct( MyLibrary $mylib ,RequestStack $request_stack)
    {
        $this->mylib = $mylib;
        $this->requestStack = $request_stack;;
    }

    
    
   
    public function recentedits()
{
   
    $sec =0;
   
      
      $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
     
     $max = 10;
        $platest =    $this->getDoctrine()->getRepository("AppBundle:Person")->findLatest($max);
         $elatest =    $this->getDoctrine()->getRepository("AppBundle:Event")->findLatest($max);
           $ilatest =    $this->getDoctrine()->getRepository("AppBundle:Image")->findLatest($max,$sec);
       
        $latest= array_merge($platest, $elatest,$ilatest);
      usort($latest, function($a, $b) {
    return $b['date'] <=> $a['date'];
});
$output = array_slice($latest, 0, $max); 
         return $this->render('recentedits/show.html.twig', 
                   ['lang'=>$this->lang, 
                     'edits'=> $output,
                     ]);
    }
}
