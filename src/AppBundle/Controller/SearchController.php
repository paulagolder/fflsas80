<?php
// src/Controller/SearchController.php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use AppBundle\Service\MyLibrary;


class SearchController extends Controller
{
    private $lang="fr";
    private $mylib;
    private $requestStack ;
    
    public function __construct( MyLibrary $mylib ,RequestStack $request_stack)
    {
        $this->mylib = $mylib;
        $this->requestStack = $request_stack;
    }
    


    public function ShowAll(Request $request)
    {
    
       $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
    
       $pfield = $request->query->get('searchfield');
       $gfield = $request->query->get('searchfield');
   
        if (!$pfield) 
        {
            return $this->render('search/showall.html.twig', 
            [ 
              'lang' => $this->lang,
               'message' =>  'nothing.to.find',
            ]);
        }
        
        $pfield = "%".$pfield."%";
        
        $i=0;
        $people = $this->getDoctrine()->getRepository("AppBundle:Person")->findSearch($pfield);
        $results = array();
        foreach($people as $key => $person)
        {
          $pid = $person->getPersonid();
          $results['people'][$pid]['label'] = $person->getFullname();
          $results['people'][$pid]['link'] ="/".$this->lang."/person/".$pid;
        }
        
        $contents = $this->getDoctrine()->getRepository("AppBundle:Content")->findSearch($pfield);
        foreach($contents as $key => $content)
        {
         $cid = $content->getContentid();
          $content =   $this->getDoctrine()->getRepository("AppBundle:Content")->findOne($cid);
          if($content->getAccess()<2)
          {
            $sid = $content->getSubjectid();
            $results['content'][$sid]['label'] = $content->getLabel();
           $results['content'][$sid]['link'] ="/".$this->lang."/content/".$sid;
          }
        }
        
        $locations = $this->getDoctrine()->getRepository("AppBundle:Location")->findSearch($pfield);
        foreach($locations as $key => $location)
        {
          $lid = $location->getlocid();
          $results['location'][$lid]['label'] = $location->getName();
          $results['location'][$lid]['link'] ="/".$this->lang."/location/".$lid;
        }
        
         $urls = $this->getDoctrine()->getRepository("AppBundle:Url")->findSearch($pfield);
        foreach($urls as $key => $url)
        {
          $lid = $url->getId();
          $results['url'][$lid]['label'] = $url->getLabel();
          $results['url'][$lid]['link'] ="/".$this->lang."/url/".$lid;
        }
         
        $biblos = $this->getDoctrine()->getRepository("AppBundle:Biblo")->findSearch($pfield);
        foreach($biblos as $key => $biblo)
        {
          $lid = $biblo->getBookId();
          $results['biblo'][$lid]['label'] = $biblo->getTitle();
          $results['biblo'][$lid]['link'] ="/".$this->lang."/biblo/".$lid;
        }
        
         $ref_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findTexts($pfield);
         foreach($ref_ar as $key => $oref_ar)
         {
           $obtype = $key;
           switch ($obtype) 
           {
              case "person":
                 foreach($oref_ar as $key => $ref)
                 {
                      $pid = $key;
                      $person = $this->getDoctrine()->getRepository("AppBundle:Person")->findOne($pid);
                      if($person)
                        $results['people'][$pid]['label'] = $person->getFullname();
                        else
                         $results['people'][$pid]['label'] = "  notfound ";
                      $results['people'][$pid]['link'] ="/".$this->lang."/person/".$pid;
                 }
               break;
              case "event":
                 foreach($oref_ar as $key => $ref)
                 {
                      $pid = $key;
                      $event = $this->getDoctrine()->getRepository("AppBundle:Event")->findOne($pid);
                      if($event)
                      {
                      $results['events'][$pid]['label'] = $event->getLabel();
                      $results['events'][$pid]['link'] ="/".$this->lang."/event/".$pid;
                      }
                      else
                      {
                       $results['events'][$pid]['label'] = "notfound event ".$pid;
                         $results['events'][$pid]['link'] ="/".$this->lang."/event/".$pid;
                       }
                 }
               break;
             case "image":
                 foreach($oref_ar as $key => $ref)
                 {
                      $pid = $key;
                      dump($oref_ar);
                      dump($key);
                      $image =   $this->getDoctrine()->getRepository("AppBundle:Image")->findOne($key);
                      ##ignore images with access > public 
                      if($image  && $image->getAccess()<2)
                      {
                      $text_ar =  $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup('image',$pid);
                    //  $results['images'][$pid]['label'] = $this->mylib->getText($text_ar,'title',$this->lang)['comment'];
                    $label = $this->mylib->getText($text_ar,'title',$this->lang);
                    if(!$label || $label =="No text found")
                    {
                      $label= $image->getName();
                      }
                    $results['images'][$pid]['label'] = $label;
                       $results['images'][$pid]['link'] ="/".$this->lang."/image/".$pid;
                     }
                 }
              break;
               case "content":
                 foreach($oref_ar as $key => $ref)
                 {
                      $pid = $key;
                       $content =   $this->getDoctrine()->getRepository("AppBundle:Location")->findOne($pid);
                       if($content->getAccess()<2)
                       {
                      $text_ar =  $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup('content',$pid);
                      $results['content'][$pid]['label'] = $this->mylib->getText($text_ar,'title',$this->lang);
                      $results['content'][$pid]['link'] ="/".$this->lang."/content/".$pid;
                      }
                 }
              break;
               case "location":
                 foreach($oref_ar as $key => $ref)
                 {
                      $pid = $key;
                      $location =   $this->getDoctrine()->getRepository("AppBundle:Location")->findOne($pid);
                      if($location != null)
                      {
                      $results['locations'][$pid]['label'] = $location->getName();
                      $results['locations'][$pid]['link'] ="/".$this->lang."/location/".$pid;
                      }
                 }
                    break;
               case "url":
                 foreach($oref_ar as $key => $ref)
                 {
                      $pid = $key;
                      $url =   $this->getDoctrine()->getRepository("AppBundleUrl")->findOne($pid);
                      if($url != null)
                      {
                      $results['urls'][$pid]['label'] = $url->getLabel();
                      $results['urlss'][$pid]['link'] ="/".$this->lang."/url/".$pid;
                      }
                 }
                    break;
               case "biblo":
                 foreach($oref_ar as $key => $ref)
                 {
                      $pid = $key;
                      $biblo =   $this->getDoctrine()->getRepository("AppBundle:Biblo")->findOne($pid);
                      if($biblo != null)
                      {
                      $results['biblos'][$pid]['label'] = $biblo->geTitle();
                      $results['biblos'][$pid]['link'] ="/".$this->lang."/biblo/".$pid;
                      }
                 }
                      break;
            }
         }
    
           if (count($results)<1) 
           {
            return $this->render('search/showall.html.twig', 
            [ 
               'message' =>  'rien.trouver',
               'searchkey'=>$gfield,
            ]);
           }
           
           return $this->render('search/showall.html.twig', 
                       [ 
                         'message' => "",
                         'searchkey'=>$gfield,
                         'results'=> $results,
                       ]);
    }
    
    
  }  
    
   
