<?php

// src/Controller/NewsController.php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use  AppBundle\Controller\Security;

use AppBundle\Service\MyLibrary;

class NewsController extends Controller
{
    private $lang="fr";
    private $mylib;
    private $requestStack ;
    
    public function __construct( MyLibrary $mylib ,RequestStack $request_stack)
    {
        $this->mylib = $mylib;
        $this->requestStack = $request_stack;;
    }
    
    
    
    
    public function findnews()
    {
        
        $sec =0;
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $max = 10;
        $news=    $this->getDoctrine()->getRepository("AppBundle:Content")->findNews($max);
     //   usort($news, function($a, $b) {
    //        return $b['date'] <=> $a['date'];
    //    });
      //  $output = array_slice($news, 0, $max); 
        return $this->render('news/show.html.twig', 
        ['lang'=>$this->lang, 
        'newsitems'=> $news,
        ]);
    }
}
