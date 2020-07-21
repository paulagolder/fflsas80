<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use AppBundle\Service\MyLibrary;
use AppBundle\Entity\Incident;
use AppBundle\Entity\IncidentType;
use AppBundle\Entity\Participation;
use AppBundle\Entity\person;
use AppBundle\Entity\event;
use AppBundle\Form\IncidentForm;

class IncidentController extends Controller
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
        return $this->render('incident/index.html.twig', [
            'controller_name' => 'IncidentController',
        ]);
    }
    
    
     public function Showall()
     {
         $lib =  $this->mylib ;
         $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
         $incidents = $this->getDoctrine()->getRepository("AppBundle:Incident")->findAll();
        if (!$incidents ) 
        {
            return $this->render('incident/showall.html.twig', [ 'message' =>  'Incidents not Found',]);
        }
        foreach($incidents as $key =>$incident)
        {
            $personid = $incident['personid'];
            $person =  $this->getDoctrine()->getRepository("AppBundle:Person")->findOne($personid);
            $personname = Person::fullname($person);;
            $eventid = $incident['eventid'];
           # $test_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("title",$eventid,$this->lang);
              $etext_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$eventid);
                 $etitle= $lib->selectText( $etext_ar,"title",$this->lang);
            $incidents[$key]['label'] = $personname.":".$etitle.":".$incident['typename'];
             $incidents[$key]['link'] ="/admin/incident/".$incident['incidentid'];
        }
     
        return $this->render('incident/showall.html.twig', 
                  [
                    'lang'=>$this->lang,
                     'message' =>  '',
                     'heading' =>  'all.incidents',
                     'incidents'=> $incidents,
                  ]);
     
     }
     
       public function Showone($inid)
     {
      $lib =  $this->mylib ;
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
         $incident = $this->getDoctrine()->getRepository("AppBundle:Incident")->findOne($inid);
        if (!$incident ) 
        {
            return $this->render('incident/showone.html.twig', 
            [
             'lang'=>$this->lang,
             'message' =>  'Incident '.$inid. ' not Found',
             ]);
        }
        if($incident->getSdate()>0)
        {
          $incident->setSdate( $this->mylib->formatDate($incident->getSdate(),$this->lang));
        }
        else
        {
           $incident->setSdate( "");
        }
        $person =  $this->getDoctrine()->getRepository("AppBundle:Person")->findOne($incident->getPersonid());
        $event =  $this->getDoctrine()->getRepository("AppBundle:Event")->findOne($incident->getEventid());
         $eventlabel=$event->getLabel();
        $etext_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup('event',$incident->getEventid());
          $eventlabel= $lib->selectText( $etext_ar,"title",$this->lang);
        $itype = $this->getDoctrine()->getRepository("AppBundle:IncidentType")->findOne($incident->getItypeid());
        $location = $this->getDoctrine()->getRepository("AppBundle:Location")->findOne($incident->getLocid());
        if($location)
           $location->link = "/".$this->lang."/location/".$incident->getLocid();
        $ilabel = $itype->getLabel();
        return $this->render('incident/showone.html.twig', 
                [ 
                   'lang'=>$this->lang,
                   'message' =>  '',
                   'eventlabel'=>$eventlabel,
                   'personname' => $person->getFullname(),
                   'incident'=> $incident,
                   'location'=>$location,
                   'label'=>$ilabel,
                   'returnlink'=>"/".$this->lang."/person/".$person->getPersonid(),
                ]);
     
     }
     
     public function edit($inid)
    {
        
        $request = $this->requestStack->getCurrentRequest();
        $user = $this->getUser();
        $time = new \DateTime();
        if($inid>0)
        {
            $incident = $this->getDoctrine()->getRepository('AppBundle:Incident')->findOne($inid);
        }
        if(! isset($incident))
        {
            $incident = new Incident();
        }
        $form = $this->createForm(IncidentForm::class, $incident);
      
        if ($request->getMethod() == 'POST') 
        {
            $form->handleRequest($request);
            if ($form->isValid()) 
            {
                $incident->setContributor($user->getUsername());
                $incident->setUpdateDt($time);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($incident);
                $entityManager->flush();
                $inid = $incident->getIncidentId();
                return $this->redirect("/admin/incident/".$inid);
            }
        }
         $pid = $form["personid"]->getData();
         $eid = $form["eventid"]->getData();
         $person =   $this->getDoctrine()->getRepository("AppBundle:Person")->findOne($pid);
         $event =   $this->getDoctrine()->getRepository("AppBundle:Event")->findOne($eid);
         $incidenttypes =   $this->getDoctrine()->getRepository("AppBundle:IncidentType")->findAll();
         $participations =  $this->getDoctrine()->getRepository("AppBundle:Participant")->findParticipationsbyEntityPerson($eid,$pid);
         return $this->render('incident/edit.html.twig', array(
            'form' => $form->createView(),
            'eventlabel'=>$event->getLabel(),
            'personname'=> $person->getSurname(),
            'incidentid'=>$inid,
            'itypes'=>$incidenttypes,
            'itypeid'=>$incident->getItypeid(),
            'returnlink'=>'/admin/participant/'.$participations[0]->getParticipationid(),
            ));
    }
    
     public function new($eid,$pid)
    {
        
        $request = $this->requestStack->getCurrentRequest();
        
            $incident = new Incident();
            $incident->setEventid($eid);
            $incident->setPersonid($pid);
      
        $form = $this->createForm(IncidentForm::class, $incident);
        
        if ($request->getMethod() == 'POST') 
        {
            #$form->bindRequest($request);
            $form->handleRequest($request);
            if ($form->isValid()) 
            {
                // perform some action, such as save the object to the database
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($incident);
                $entityManager->flush();
                $inid= $incident->getIncidentid();
                return $this->redirect("/admin/incident/".$inid);
            }
        }
         $pid = $form["personid"]->getData();
         $eid = $form["eventid"]->getData();
         $person =   $this->getDoctrine()->getRepository("AppBundle:Person")->findOne($pid);
         $event =   $this->getDoctrine()->getRepository("AppBundle:Event")->findOne($eid);
         $incidenttypes =   $this->getDoctrine()->getRepository("AppBundle:IncidentType")->findAll();
        return $this->render('incident/new.html.twig', array(
            'form' => $form->createView(),
            'eventlabel'=>$event->getLabel(),
            'personname'=> $person->getFullname(),
            'itypes'=>$incidenttypes,
             'itypeid'=>1,
            'returnlink'=>'/admin/person/'.$pid,
            ));
    }
    
     public function newtype($inid,Request $request)
    {
        $label = $request->query->get('_newlabel');
        
            $newtype = new IncidentType();
            $newtype->setlabel($label);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($newtype);
                $entityManager->flush();
                return $this->redirect("/admin/incident/".$inid);
           
    }
    
    public function delete($inid)
    {
        $incident = $this->getDoctrine()->getRepository('AppBundle:Incident')->findOne($inid);
        $eventid = $incident-> getEventid();
        $personid = $incident->getPersonid();
        $participations = $this->getDoctrine()->getRepository('AppBundle:Participant')->findParticipationsbyEntityPerson($eventid, $personid);
        $this->getDoctrine()->getRepository("AppBundle:Incident")->delete($inid);
        return $this->redirect("/admin/participant/".$participations[0]->getParticipationId());
    }
     
     
    function xtransformtoIncident($incident_ar)
    {
        $incident = new Incident();
         $incident->setIncidentid($incident_ar['incidentid']);
        $incident->setEventid($incident_ar['eventid']);
        $incident->setPersonid($incident_ar['personid']);
        $incident->setItypeid($incident_ar['itypeid']);
        $incident->setNamerecorded($incident_ar['name_recorded']);
        $incident->setRank($incident_ar['rank']);
        $incident->setRole($incident_ar['role']);
        $incident->setsdate($incident_ar['sdate']);
        $incident->setEdate($incident_ar['edate']);
        $incident->setSequence($incident_ar['sequence']);
        $incident->setComment($incident_ar['comment']);
        return $incident;
    
    }
     
     
     
}
