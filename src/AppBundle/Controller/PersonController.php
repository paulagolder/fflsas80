<?php
// src/Controller/PersonController.php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;
//use Symfony\Component\OptionsResolver\Options;

use Symfony\Component\HttpFoundation\Response;



use AppBundle\Form\PersonFormType;
use AppBundle\Entity\Person;
use AppBundle\Entity\location;
use AppBundle\Entity\Event;
use AppBundle\Entity\Text;
use AppBundle\Controller\LinkrefController;
use AppBundle\Service\MyLibrary;
use AppBundle\Service\FLSASImage;
use AppBundle\MyClasses\eventTree;
use AppBundle\MyClasses\eventTreeNode;


use Dompdf\Dompdf;
use Dompdf\Options;


class PersonController extends Controller
{
    private $lang="fr";
    private $mylib;
    private $requestStack ;
    private $translator ;

    
    
    
    public function __construct( MyLibrary $mylib ,RequestStack $request_stack,TranslatorInterface $translator)
    {
        $this->mylib = $mylib;
        $this->requestStack = $request_stack;
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $this->translator  =  $translator;
        #$this->translator->setLocale($this->mylib->toLocale($this->lang));
    }
    

    
    public function index()
    {
        return $this->render('person/index.html.twig', [
        'controller_name' => 'personController',
        ]);
    }
    
    
    public function Showall()
    {

        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $people = $this->getDoctrine()->getRepository("AppBundle:Person")->findAll();
        if (!$people) {
            return $this->render('person/showall.html.twig', [ 'message' =>  'People not Found',]);
        }        
        foreach($people as $person)
        {
           $person->link = "/".$this->lang."/person/".$person->getPersonid();
        }
        return $this->render('person/showall.html.twig', 
                              [ 
                                'lang' => $this->lang,
                                'message' =>  '' ,
                                'heading' => 'the.men',
                                'people'=> $people,
                                ]);

    }
    
    public function Showsidebar()
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $people = $this->getDoctrine()->getRepository("AppBundle:Person")->findAll();
        
        if (!$people) {
            return $this->render('person/showsidebar.html.twig', [ 'message' =>  'People not Found',]);
        }        
        foreach($people as $person)
        {
           $person->link = "/".$this->lang."/person/".$person->getPersonid();
        }
        return $this->render('person/showsidebar.html.twig', 
                              [ 
                                'lang' => $this->lang,
                                 'message' =>  '' ,
                                 'heading' =>  'the.men',
                                 'people'=> $people,
                                ]);
    }
    
    
    public function ShowRoH()
    {
        
        return $this->render('person/showRoH.html.twig');
    }
    
    public function Showone($pid)
    {
        $lib =  $this->mylib ;
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $person = $this->getDoctrine()->getRepository("AppBundle:Person")->findOne($pid);
        if (!$person) 
        {
            return $this->render('person/showone.html.twig', [ 'lang'=>$this->lang,  'message' =>  'Person '.$pid.' not Found',]);
        }
          $person->link = "/".$this->lang."/person/".$person->getPersonid();
        $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("person",$pid);
        $textcomment = $lib->selectText($text_ar,"comment",$this->lang);
        
        $participations = $this->getDoctrine()->getRepository("AppBundle:Participant")->findParticipations($pid);
        $pevents = array();
        
        $i=0;
        foreach($participations as $participation)
        {
            $ppid = $participation->getEventid();
            $pevents[$i] = $this->getDoctrine()->getRepository("AppBundle:Event")->findOne($ppid);
            $parents= $pevents[$i]->ancestors;
            if(count($parents))
            {
                $l = count($parents);
                for($ip=0; $ip<$l;$ip++)
                {
                    $psid = $parents[$ip]->getEventid();
                    $ptext_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$psid);
                    //echo("=+=+=".$this->lang);
                    $parents[$ip]->title= $lib->selectText( $ptext_ar,"title",$this->lang);
                    $parents[$ip]->link = "/".$this->lang."/event/".$psid;
                }
                $pevents[$i]->ancestors =$parents;
            }
            $etexts_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$ppid);
            $pevents[$i]->title = $this->mylib->selectText($etexts_ar,'title',$this->lang);
            if(   $pevents[$i]->title ==null) $pevents[$i]->title = "pas trouver";
            $pevents[$i]->link = "/".$this->lang."/event/".$ppid;
            $pevents[$i]->participantinfo = $participation->getrank();
            if($participation->getRole())
            {
              $pevents[$i]->participantinfo .= " (".$participation->getRole().")";
            }
            $i++;
        }
        $topevent =  $this->getDoctrine()->getRepository("AppBundle:Event")->findTop();
        $topevent->title = " TOP ".  $topevent->getLabel();
        $topevent->link = "/".$this->lang."/event/".$topevent->getEventid();
       // $topevent->particpantinfo = $participation->getrank()."(".$participation->getRole().")";
        $evt = new eventTree($topevent);
        $evt->buildTree($pevents);
        $evt->sortTree();
        $ref_ar = $this->getDoctrine()->getRepository("AppBundle:Imageref")->findGroup("person",$pid);
        $images= array();
        $i=0;
        foreach($ref_ar as $key => $ref)
        {

            $imageid = $ref_ar[$key]['imageid'];
            $image = $this->getDoctrine()->getRepository("AppBundle:Image")->findOne($imageid);
            
            if($image)
            {
                $this->mylib->setFullpath($image);
                $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("image",$imageid);
                $images[$i]['fullpath']= $image->fullpath;
                $images[$i]['title'] = $lib->selectText($text_ar,'title',$this->lang);
                $images[$i]['link'] = "/".$this->lang."/image/".$imageid;
                $i++;
            }

        }
        $incidents =  $this->getDoctrine()->getRepository("AppBundle:Incident")->seekByPerson($pid);
        foreach( $incidents as $key=>$incident )
        {
            $incidents[$key]['label'] = $this->formatIncident($incident);
            $incidents[$key]['link'] =  "/".$this->lang."/incident/".$incident['incidentid'];
        }
        $mess = '';
        $linkrefs = $this->get('linkref_service')->getLinks("person",$pid, $this->lang);
        return $this->render('person/showone.html.twig', 

        [ 'lang' => $this->lang,
        'message' =>  $mess,
        'person'=> $person, 
        'text'=> $textcomment,
        'images'=> $images,
        'eventtree'=>$evt,
        'refs'=>$linkrefs,
        'incidents'=>$incidents,
        ]);

    }
    
    public function formatIncident($incident)
    {
        $text = $this->translator->trans($incident['label'], [], 'itypes');
        $comment = $incident['comment'];
        if($incident['locid']>1  ) // not including world
        {
            $at = $this->translator->trans('at.place');
            $lid = $incident['locid'];
            $location =   $this->getDoctrine()->getRepository("AppBundle:Location")->findOne($lid);
           // $text .= " $at ". $location->getName();
           $locname = $location->getName();
           if(strpos($comment, $locname)===false)
           {
           $text .=  " ".$location->getName();
           }
        }
        //else
        if( $incident['comment']!= "")
        {
            $at = $this->translator->trans('at.place');
           // $text .= " $at ". $incident['comment'];
           $text .= " ". $incident['comment'];
        }
        if( $incident['sdate'] >0 )
        {
            $on =  $this->translator->trans('on.date');
            $sdate= $incident['sdate'];
            $sdate = $this->mylib->formatdate($sdate,$this->lang);
            $text .= " ". $sdate;
        }
        return $text;
    }
    
    public function addbookmark($pid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $this->bookmark($pid);
        return $this->redirect('/admin/person/'.$pid);

    }
    
    public function addUserbookmark($pid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $this->bookmark($pid);
        return $this->redirect("/".$this->lang.'/person/'.$pid);
    }
    
    
    private function bookmark($pid)
    {

        $person =  $this->getDoctrine()->getRepository("AppBundle:Person")->findOne($pid);
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $plist = $session->get('personList');
        if($plist == null)
        {
            $plist = array();
        }
        if( !array_key_exists ( $pid , $plist))
        {
            $newperson = array();
            $newperson['id'] = $pid;
            $newperson["label"]= $person->getFullname();
            $plist[$pid] = $newperson;
            $session->set('personList', $plist);
        }
    }
    

    
    public function pdfone($pid)
    {
        $lib =  $this->mylib ;

        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $person = $this->getDoctrine()->getRepository("AppBundle:Person")->findOne($pid);
        $person->link = "/".$this->lang."/person/".$person->getPersonid();
        if (!$person) 
        {
            return $this->render('person/showone.html.twig', [ 'lang'=>$this->lang,  'message' =>  'Person '.$pid.' not Found',]);
        }
        
        $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("person",$pid);
        $textcomment = $lib->selectText($text_ar,"comment",$this->lang);

        
        $participations = $this->getDoctrine()->getRepository("AppBundle:Participant")->findParticipations($pid);
        $pevents = array();
        
        $i=0;
        foreach($participations as $participation)
        {

            $ppid = $participation->getEventid();
            $pevents[$i] = $this->getDoctrine()->getRepository("AppBundle:Event")->findOne($ppid);
            $parents= $pevents[$i]->ancestors;
            if(count($parents))
            {
                $l = count($parents);
                for($ip=0; $ip<$l;$ip++)
                {
                    $psid = $parents[$ip]->getEventid();
                    $ptext_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$psid);
                    //echo("=+=+=".$this->lang);
                    $parents[$ip]->title= $lib->selectText( $ptext_ar,"title",$this->lang);
                    $parents[$ip]->link = "/".$this->lang."/event/".$psid;
                }
                $pevents[$i]->ancestors =$parents;
            }
            $etexts_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$ppid);
            $pevents[$i]->title = $this->mylib->selectText($etexts_ar,'title',$this->lang);
            if(   $pevents[$i]->title ==null) $pevents[$i]->title = "pas trouver";
            $pevents[$i]->link = "/".$this->lang."/event/".$ppid;
            $i++;

        }
        $topevent =  $this->getDoctrine()->getRepository("AppBundle:Event")->findTop();
        $topevent->title = " TOP ".  $topevent->getLabel();
        $topevent->link = "/".$this->lang."/event/".$topevent->getEventid();
        $evt = new eventTree($topevent);
        $evt->buildTree($pevents);
        $ref_ar = $this->getDoctrine()->getRepository("AppBundle:Imageref")->findGroup("person",$pid);
        $images= array();
        $i=0;
        foreach($ref_ar as $key => $ref)
        {

            $imageid = $ref_ar[$key]['imageid'];
            $image = $this->getDoctrine()->getRepository("AppBundle:Image")->findOne($imageid);
            
            if($image)
            {
                $this->mylib->setFullpath($image);
                $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("image",$imageid);
                $images[$i]['fullpath']= $image->fullpath;
                $images[$i]['title'] = $lib->selectText($text_ar,'title',$this->lang);
                $images[$i]['link'] = "/".$this->lang."/image/".$imageid;
                $i++;
            }

        }
        $incidents =  $this->getDoctrine()->getRepository("AppBundle:Incident")->seekByPerson($pid);
        foreach( $incidents as $key=>$incident )
        {
            $incidents[$key]['label'] = $this->formatIncident($incident);
            $incidents[$key]['link'] =  "/".$this->lang."/incident/".$incident['incidentid'];
        }
        $mess = '';
        
        
        $linkrefs = $this->get('linkref_service')->getLinks("person",$pid,$this->lang);
        
        $header = $this->renderView('person/pdf_header.html.twig', array(
            'name' => $person->getFullname(),
            ));
            
            $footer = $this->renderView('person/pdf_footer.html.twig', array(
                'date' => "today",
                ));
                
                
                
                $html = $this->renderView('person/pdfone.html.twig', 
                [ 'lang' => $this->lang,
                'message' =>  $mess,
                'person'=> $person, 
                'text'=> $textcomment,
                'images'=> $images,
                'eventtree'=>$evt,
                'refs'=>$linkrefs,
                'incidents'=>$incidents,
                'header_html' => $header,
                'footer_html' => $footer,
                ]);
                
                
                
                $options = new Options();
                $options->set('isRemoteEnabled', true);
                $dompdf = new Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $outfile = "p".$pid.".pdf";
                $dompdf->stream($outfile);
                $dompdf->stream();
                
                
    }
    
     public function personsearch(Request $request)
    {
        $message="";
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        
        $pfield = $request->query->get('searchfield');
        $gfield = $request->query->get('searchfield');
        
        if (!$pfield) 
        {
            $people = $this->getDoctrine()->getRepository("AppBundle:Person")->findAll();
            $subheading =  'found.all';
        }
        else
        {
            $pfield = "%".$pfield."%";
            $people = $this->getDoctrine()->getRepository("AppBundle:Person")->findSearch($pfield);
            $subheading =  'found.with';
        }
        
        
        if (count($people)<1) 
        {
             $subheading = 'nothing.found.for';
        }
        else
        {
            foreach($people as $person)
            {
                $person->link = "/admin/person/addbookmark/".$person->getId();
            }
            
        }
        
        
        return $this->render('person/personsearch.html.twig', 
        [ 
        'lang'=>$this->lang,
        'message' => $message,
        'heading' =>  'search.the.men',
        'subheading' =>  $subheading,
        'searchfield' =>$gfield,
        'people'=> $people,
        
        ]);
    }
    

}
