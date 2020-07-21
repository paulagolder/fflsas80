<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\RequestStack;

use AppBundle\Form\EventForm;
use AppBundle\Service\MyLibrary;
use AppBundle\Entity\Event;
use AppBundle\Entity\Person;
use AppBundle\Entity\Imageref;
use AppBundle\Entity\Linkref;
use AppBundle\Entity\Participant;


class AdminEventController extends Controller
{

    private $lang="fr";
    private $mylib;
    private $requestStack ;
    
    public function __construct( MyLibrary $mylib ,RequestStack $request_stack)
    {
        $this->mylib = $mylib;
        $this->requestStack = $request_stack;;
    }

  
    public function index()
     {
         return $this->render('event/index.html.twig', [
         'controller_name' => 'EventController',
         ]);
     }
     
    
     
     public function Showall()
     {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
         $Events = $this->getDoctrine()->getRepository("AppBundle:Event")->findAll();
     if (!$Events) 
     {
         return $this->render('event/showall.html.twig', [ 'message' =>  'Events not Found',]);
     }
     
     
     return $this->render('event/adminshowall.html.twig',
                         [
                            'lang' => $this->lang,
                            'message' =>  '',
                            'heading' =>  'all Events ('.count($Events).')',
                            'events'=> $Events,]);
     
     }
     
     public function Editone($eid)
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
              $url = "/admin/event/".$pid;
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
               $children[$i]['fstartdate'] = $lib->formatdate($child->getStartdate(),$this->lang);
                   $children[$i]['order'] = trim($child->getStartdate().substr("000000".$child->getSequence(),-5));
             $ptext_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$pid);
      
             $children[$i]['title'] =  $this->mylib->selectText($ptext_ar, "title", $this->lang );
          
             if( $children[$i]['title'] =='')  $children[$i]['title']=$child->getLabel();
             $url = "/admin/event/".$pid;
             $children[$i]['link'] =  $url ;

           }
          // usort($children, function ($item1, $item2) {return ($item1['startdate'] <=> $item2['startdate']);});
           uasort($children,  array("AppBundle\Controller\EventController", 'datecmp'));
           $event->children = $children;
        }
        
        // this is a fix because title gets changed  and I cannot find out why 
              $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$eid);
        $texttitle = $lib->selectText($text_ar, "title",$this->lang);
        $event->title = $texttitle;
         if($event->title == "")
        {
          $event->title = $event->getLabel();
        }
      
        $textcomment =  $lib->selectText($text_ar, "comment",$this->lang);
        
         $ref_ar = $this->getDoctrine()->getRepository("AppBundle:Imageref")->findGroup("event",$eid);
        $images= array();
        $i=0;
        foreach($ref_ar as $key => $ref)
        {
           $imageid = $ref_ar[$key]['imageid'];
           $images[$i]['imageid']= $imageid;
           $images[$i]['refid']= $ref_ar[$key]['id'];
           $image = $this->getDoctrine()->getRepository("AppBundle:Image")->findOne($imageid);
           $this->mylib->setFullpath($image);
           $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("image",$imageid);
           $images[$i]['fullpath']= $image->fullpath;
           $images[$i]['title'] = $this->mylib ->selectText($text_ar,'title',$this->lang);
           $images[$i]['link'] = "/admin/image/".$imageid;
           $i++;
        }
        $participations = $this->getDoctrine()->getRepository("AppBundle:Participant")->findParticipants($eid);
        $participants = array();
        foreach($participations as $key=>$aparticipant)
        {
            $participants[$key]['label'] = $aparticipant['label'];;
            $participants[$key]['link'] = "/admin/person/".$aparticipant['personid'];
        }
       
       if($event->getLocid())
       {
         $location = $this->getDoctrine()->getRepository("AppBundle:Location")->findOne($event->getLocid());
         if($location)
         {
         $event->location['name'] = $location->getName();
         $event->location['link'] = "/admin/location/".$location->getLocid();
         }
         else
              dump($event); //error condition
       }
        
       // $linkrefs =$this->getDoctrine()->getRepository("AppBundle:Linkref")->findGroup('event',$eid);
          $linkrefs = $this->get('linkref_service')->getLinks("event",$eid, $this->lang);
        
        return $this->render('event/editone.html.twig', [ 
             'lang' => $this->lang,
             'message' =>  '',
             'heading' =>  'Event '.$eid.' found',
             'event'=> $event,
              'title'=> $texttitle,
             'text'=> $textcomment,
             'images'=>$images,
             'refs'=>$linkrefs,
             'participants' => $participants,
              'source'=>"/admin/event/".$eid,
               'objid'=>$eid,   
               'returnlink'=> "/".$this->lang."/event/".$eid,
              ]);
        
     }
     
     
    
     
  
     public function Showtop()
     {
         $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
         $topevent  = $this->getDoctrine()->getRepository("AppBundle:Event")->findTop();
         return $this->Editparticipants($topevent->getEventId());
     }
    
      public function editdetail($eid)
    {
        
        $request = $this->requestStack->getCurrentRequest();
        $user = $this->getUser();
        $time = new \DateTime();
        if($eid>0)
        {
            $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOne($eid);
        }
        if(! isset($event))
        {
            $eid = -$eid;
            $event = new Event();
            $event-> setParent( $eid );
        }
        $form = $this->createForm(EventForm::class, $event);
        
        if ($request->getMethod() == 'POST') 
        {
            #$form->bindRequest($request);
            $form->handleRequest($request);
            if ($form->isValid()) 
            {
                $event->setContributor($user->getUsername());
                $event->setUpdateDt($time);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($event);
                $entityManager->flush();
                return $this->redirect("/admin/event/".$eid);
            }
        }
        
        return $this->render('event/editdetail.html.twig', array(
            'form' => $form->createView(),
            'objid'=>$eid,
            'returnlink'=>'/admin/event/'.$eid,
            'source'=>'/admin/event/'.$eid,
            ));
    }
   
   
     
     public function addimage($eid,$iid)
    {
        $imageref =  $this->getDoctrine()->getRepository("AppBundle:Imageref")->findMatch("event",$eid,$iid);
        if(!$imageref)
        {
        $em = $this->getDoctrine()->getManager();
        $newp = new Imageref();
        $newp->setObjid((int)$eid);
        $newp->setImageid((int)$iid);
        $newp->setObjecttype('event');
        $em->persist($newp);
        $em->flush();
        }
        return $this->redirect('/admin/event/'.$eid);
        
    }
    
    public function addContent($eid,$cid)
    {
   
        $em = $this->getDoctrine()->getManager();
        $newp = new Linkref();
        $newp->setObjid((int)$eid);
        $newp->setpath("/content/".$cid);
        $newp->setObjecttype('event');
          $newp->setLabel('Content:'.$cid);
        $em->persist($newp);
        $em->flush();
        return $this->redirect('/admin/event/'.$eid);
        
    }
    
    
      public function addBiblo($eid,$bid)
    {
        $biblo =  $this->getDoctrine()->getRepository("AppBundle:Biblo")->findOne($bid);
        $em = $this->getDoctrine()->getManager();
        $newp = new Linkref();
        $newp->setObjid((int)$eid);
        $newp->setpath("/biblo/".$bid);
        $newp->setObjecttype('event');
          $newp->setLabel($biblo->getLabel());
        $em->persist($newp);
        $em->flush();
        return $this->redirect('/admin/event/'.$eid);
        
    }
    
      
      public function addUrl($eid,$uid)
    {
        $url =  $this->getDoctrine()->getRepository("AppBundle:Url")->findOne($uid);
        $em = $this->getDoctrine()->getManager();
        $newp = new Linkref();
        $newp->setObjid((int)$eid);
        $newp->setpath("/url/".$uid);
        $newp->setObjecttype('event');
          $newp->setLabel($url->getLabel());
        $em->persist($newp);
        $em->flush();
        return $this->redirect('/admin/event/'.$eid);
        
    }
    
    public function addparticipant($eid,$pid)
    {
        $user = $this->getUser();
        $time = new \DateTime();
        $em = $this->getDoctrine()->getManager();
        $participations =  $this->getDoctrine()->getRepository("AppBundle:Participant")->findParticipationsbyEntityPerson($eid,$pid);
       if(!count($participations)>0)
       {
        $newp = new Participant();
        $newp->setEventid((int)$eid);
        $newp->setPersonid((int)$pid);
        $newp->setContributor($user->getUsername());
        $newp->setUpdateDt($time);
        $em->persist($newp);
        $em->flush();
      
        }
        return $this->redirect('/admin/event/'.$eid);
        
    }
    
     public function setlocation($eid,$lid)
    {
       $user = $this->getUser();
        $time = new \DateTime();
        $em = $this->getDoctrine()->getManager();
        $event =  $this->getDoctrine()->getRepository("AppBundle:Event")->findOne($eid);
        $event->setLocid((int)$lid);
         $event->setContributor($user->getUsername());
         $event->setUpdateDt($time);
        $em->persist($event);
        $em->flush();
        return $this->redirect('/admin/event/'.$eid);
    }
    
    public function deleteLocation($eid)
    {
        $user = $this->getUser();
        $time = new \DateTime();
        $em = $this->getDoctrine()->getManager();
        $event =  $this->getDoctrine()->getRepository("AppBundle:Event")->findOne($eid);
        $event->setLocid(null);
         $event->setContributor($user->getUsername());
         $event->setUpdateDt($time);
        $em->persist($event);
        $em->flush();
        return $this->redirect('/admin/event/'.$eid);
        
    }
      public function removeimageref($eid,$irid)
    {
     $image = $this->getDoctrine()->getRepository("AppBundle:Imageref")->findOne($irid);
     $em = $this->getDoctrine()->getManager();
      $em->remove($image);
     $em->flush();
        return $this->redirect('/admin/event/'.$eid);
    }
    
    
    public function removelink($eid,$lid)
    {
     $link = $this->getDoctrine()->getRepository("AppBundle:Linkref")->findOne($lid);
     $em = $this->getDoctrine()->getManager();
      $em->remove($link);
     $em->flush();
        return $this->redirect('/admin/event/'.$eid);
        
    }
    
    public function delete($eid)
    {
         $event =  $this->getDoctrine()->getRepository("AppBundle:Event")->findOne($eid);
       $peid = $event->ancestors[0]->getEventId(); 
     $em = $this->getDoctrine()->getManager();
      $em->remove($event);
     $em->flush();
     // remove image links, reflinkk, participants, texts paul to fix
        return $this->redirect('/admin/event/'.$peid);
        
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
     
        return $this->redirect('/admin/event/'.$eid);
        
    }
    
    public function eventlist()
    {
       $events = $this->getDoctrine()->getRepository("AppBundle:Event")->findAll();
       $frevents= array();
       $enevents= array();
        foreach($events as $event)
            {
                $eventid = $event->getEventid();
                $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$eventid);
                $frtitle = $this->mylib->selectText($text_ar, "title","fr");
                 $entitle = $this->mylib->selectText($text_ar, "title","en");
                $frlink = "/fr/event/".$eventid;
                 $enlink = "/en/event/".$eventid;
                 $enevents[$entitle]=$enlink;
                $frevents[$frtitle]=$frlink;
            }
            
            ksort($enevents, 4);
            ksort($frevents, 4);
            
            
            
            $fpfr = fopen("docs/fr/actions.htm","w");
            foreach($frevents as $key=>$link)
            {
                fwrite($fpfr, "<div class='event' > <a href='".$link."'>".$key."</a></div>\n" );
            }
            fclose($fpfr);
            
            
            $fpen = fopen("docs/en/actions.htm","w");
            foreach($enevents as $key=>$event)
            {
                fwrite($fpen, "<div class='event' > <a href='".$link."'>".$key."</a></div>\n" );
            }
            fclose($fpen);
            
            $mess = "event.files.produced";
            return $this->redirect('/accueil/message/'.$mess);
        
       
    }
}
