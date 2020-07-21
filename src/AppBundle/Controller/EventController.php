<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\RequestStack;

use AppBundle\Service\MyLibrary;
use AppBundle\Entity\Event;
use AppBundle\Entity\Text;
use AppBundle\Entity\Person;
use AppBundle\Entity\Participant;


class EventController extends Controller
{
    
    private $lang="fr";
    private $mylib;
    private $requestStack ;
    
    public function __construct( MyLibrary $mylib ,RequestStack $request_stack)
    {
        $this->mylib = $mylib;
        $this->requestStack = $request_stack;;
    }
    
     public static function datecmp($a, $b) 
     {
         return strnatcmp($a['order'], $b['order']);
     }

 
    
    public function Showall()
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $Events = $this->getDoctrine()->getRepository("AppBundle:Event")->findAll();
        if (!$Events) 
        {
            return $this->render('event/showall.html.twig', [ 'message' =>  'Events not Found',]);
        }
        
        
        return $this->render('event/showall.html.twig',
        [
        'lang' => $this->lang,
        'message' =>  '',
        'heading' =>  'all Events ('.count($Events).')','events'=> $Events,]);
        
    }
    
    public function Showone($eid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $lib = $this->mylib;
        $event = $this->getDoctrine()->getRepository("AppBundle:Event")->findOne($eid);
        
        if (!$event) 
        {
            return $this->render('event/showone.html.twig', 
            [ 
            'lang' => $this->lang,
            'message' =>  'Event '.$eid.' not Found',
            ]);
        }
        
        $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$eid);
        $event->title = $lib->selectText($text_ar, "title",$this->lang);
        
        $parents= $event->ancestors;
        if(count($parents))
        {
            $l = count($parents);
            for($i=0; $i<$l;$i++)
            {
                $pid = $parents[$i]->getEventid();
                $ptext_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$pid);
                $parents[$i]->title = $lib->selectText( $ptext_ar,"title",$this->lang);
                $url = "/".$this->lang."/event/".$pid;
                $parents[$i]->link =  $url ;
            }
            $event->ancestors =$parents;
        }
        
        $sdate = $event->getStartdate();
        if($sdate)
        {
            $sdate = $this->mylib->formatdate($sdate,$this->lang);
            $event->setStartdate( $sdate);
        }
        $edate = $event->getEnddate();
        if($edate)
        {
            $edate = $this->mylib->formatdate($edate,$this->lang);
            $event->setEnddate( $edate);
        }
        
        $children =  $event->children;
        if(count($children))
        {
            $l = count($children);
            for($i=0; $i<$l;$i++)
            {
                
                $pid = $children[$i]['id'];
                $child =  $this->getDoctrine()->getRepository("AppBundle:Event")->findOne($pid);
                
                $children[$i]['startdate'] = $child->getStartdate();
                 $children[$i]['sequence'] = $child->getSequence();
                 $children[$i]['order'] = trim($child->getStartdate().substr("000000".$child->getSequence(),-5));
                $children[$i]['fstartdate'] = $lib->formatdate($child->getStartdate(),$this->lang);
                
                $ptext_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$pid);
                
                $children[$i]['title'] =  $this->mylib->selectText($ptext_ar, "title", $this->lang );
                if( $children[$i]['title']=="")  $children[$i]['title']=$child->getLabel();
                
                $url = "/".$this->lang."/event/".$pid;
                $children[$i]['link'] =  $url ;
                
            }
            uasort($children,  array("AppBundle\Controller\EventController", 'datecmp'));
            $event->children = $children;
        }
        // this is a fix because title gets changed  and I cannot find out why 
        $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$eid);
        $event->title = $lib->selectText($text_ar, "title",$this->lang);
        $textcomment = $lib->selectText($text_ar,"comment", $this->lang);
        $ref_ar = $this->getDoctrine()->getRepository("AppBundle:Imageref")->findGroup("event",$eid);
        $images= array();
        $i=0;
        foreach($ref_ar as $key => $ref)
        {
            $imageid = $ref_ar[$key]['imageid'];
            $image = $this->getDoctrine()->getRepository("AppBundle:Image")->findOne($imageid);
            $this->mylib->setFullpath($image);
            $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("image",$imageid);
            #echo ( "++++". $image->getName()."----".$image->getLabel() );
            $images[$i]['fullpath']= $image->fullpath;
            $images[$i]['title'] = $this->mylib ->selectText($text_ar,'title',$this->lang);
            $images[$i]['link'] = "/".$this->lang."/image/".$imageid;
            $i++;
        }
        $participations = $this->getDoctrine()->getRepository("AppBundle:Participant")->findParticipants($eid);
        $participants = array();
        foreach($participations as $key=>$aparticipant)
        {
            $participants[$key]['label'] = $aparticipant['label'];;
            $participants[$key]['link'] = "/".$this->lang."/person/".$aparticipant['personid'];
            $participants[$key]['participantinfo'] = $aparticipant['rank'];
            if($aparticipant['role'])
            {
             $participants[$key]['participantinfo'] .=  " (".$aparticipant['role'].")";
             }
        }
        
        if($event->getLocid())
        {
            $location = $this->getDoctrine()->getRepository("AppBundle:Location")->findOne($event->getLocid());
            if($location)
            {
            $event->location['name'] = $location->getName();
            $event->location['link'] = "/".$this->lang."/location/".$location->getLocid();
            }
            else
            {
               $event->location['name'] = "inconnu";
               $event->location['link'] = "";
            
            }
        
        }
        
       // $linkrefs =$this->getDoctrine()->getRepository("AppBundle:Linkref")->findGroup('event',$eid);
           $linkrefs = $this->get('linkref_service')->getLinks("event",$eid, $this->lang);

        foreach($linkrefs as $key=>$linkref)
        {
           // $reftext_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("linkref",$linkrefs[$key]['linkid']);
          //  $linkrefs[$key]['label'] =  $this->mylib->selectText($reftext_ar,'title',$this->lang);
        }
        
        return $this->render('event/showone.html.twig', [ 
        'lang' => $this->lang,
        'message' =>  '',
        'heading' =>  'Event '.$eid.' found',
        'event'=> $event,
        'text'=> $textcomment,
        'images'=>$images,
        'refs'=>$linkrefs,
        'participants' => $participants,
        
        ]);
        
    }
    
    
    public function Showtop()
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $topevent  = $this->getDoctrine()->getRepository("AppBundle:Event")->findTop();
        return $this->Showone($topevent->getEventId());
    }
    
    public function addbookmark($eid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $event =  $this->getDoctrine()->getRepository("AppBundle:Event")->findOne($eid);
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $elist = $session->get('eventList');
        if($elist == null)
        {
          $elist = array();
        }

       if( !array_key_exists ( $eid , $elist))
       {
        $newev = array();
        $newev['id'] = $eid;
        $newev["label"]= $event->getLabel();
        $elist[$eid] = $newev;
         $session->set('eventList', $elist);
       }
     
        return $this->redirect('/'.$this->lang.'/event/'.$eid);
        
    }
    
      public function Showactions()
    {
         $lang = $this->requestStack->getCurrentRequest()->getLocale();
        return $this->render('/event/showactions.html.twig');
    }
     

  
    
}
