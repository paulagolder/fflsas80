<?php
// src/Controller/AdminPersonController.php
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

use AppBundle\Form\PersonForm;
use AppBundle\Entity\Person;
use AppBundle\Entity\Imageref;
use AppBundle\Entity\Linkref;
use AppBundle\Entity\event;
use AppBundle\Entity\Participant;
use AppBundle\Service\MyLibrary;
use App\MyClasses\eventTree;
use App\MyClasses\eventTreeNode;


class AdminPersonController extends Controller
{
    private $lang="fr";
    private $mylib;
    private $requestStack ;
    
    public function __construct( MyLibrary $mylib ,RequestStack $request_stack)
    {
        $this->mylib = $mylib;
        $this->requestStack = $request_stack;
    }
    
    
    
    
    public function Editone($pid)
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
        
        $i=0;
        $incidents =  $this->getDoctrine()->getRepository("AppBundle:Incident")->seekByPerson($pid);
        $participation_ar = array();
        foreach($participations as $participation)
        {
            $ppid = $participation->getEventid();
            $pincidents=array();
            $pi=0;
            foreach( $incidents as $incident)
            {
                if($incident['eventid'] == $ppid)
                {
                    $pincidents[$pi]=$incident; 
                    $pi++;
                }
            }
            if($pi>0)
            {
                $participation_ar[$i]['incidents']=$pincidents;
            }
            else 
            {
                $participation_ar[$i]['incidents'] = null;
            }  
            $participation_ar[$i]['id']= $participation->getparticipationid();
            $participation_ar[$i]['eventid']= $participation->getEventid();
            $participation_ar[$i]['link']= "/admin/participant/".$participation->getparticipationid();
            $label ="";
            $pevents = $this->getDoctrine()->getRepository("AppBundle:Event")->findOne($ppid);
            
            $parents= $pevents->ancestors;
            if(count($parents))
            {
                $l = count($parents);
                for($ip=0; $ip<$l && $ip< 1;$ip++)
                {
                    $psid = $parents[$ip]->getEventid();
                    $ptext_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$psid);
                    $label .= $lib->selectText( $ptext_ar,"title",$this->lang).":";
                }
                
            }
            $etexts_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$ppid);
            $label = $this->mylib->selectText($etexts_ar,'title',$this->lang)." : ".$label;
            $participation_ar[$i]['label'] =$label;
            $participation_ar[$i]['vue'] ="/".$this->lang."/event/".$participation->getEventid();
            $i++;
        }
        
        $ref_ar = $this->getDoctrine()->getRepository("AppBundle:Imageref")->findGroup("person",$pid);
        $images= array();
        $i=0;
        foreach($ref_ar as $key => $ref)
        {
            $imageid = $ref_ar[$key]['imageid'];
            $image = $this->getDoctrine()->getRepository("AppBundle:Image")->findOne($imageid);
            if($image )
            {
                $this->mylib->setFullpath($image);
            
                $itext_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("image",$imageid);
                $images[$i]['imageid']= $imageid;
                $images[$i]['fullpath']= $image->getFullpath();
                $images[$i]['title'] = $lib->selectText($itext_ar,'title',$this->lang);
                $images[$i]['link'] = "/admin/image/".$imageid;
                $i++;
            }
            else
            {
                $images[$i]['link'] = "/admin/image/".$imageid;
                $images[$i]['imageid']= $imageid;
                $images[$i]['title'] = "delete me";
                $images[$i]['fullpath']= "";
                $i++;
            }
        }
        
        $mess = '';
        $candelete = false;
        if(! $participation_ar && ! $text_ar) $candelete= true;
        $linkrefs = $this->get('linkref_service')->getLinks("person",$pid, $this->lang);
        
        return $this->render('person/editone.html.twig', 
        [ 'lang' => $this->lang,
        'message' =>  $mess,
        'candelete' => $candelete,
        'person'=> $person, 
        'text'=> $textcomment,
        'images'=> $images,
        'participants'=>$participation_ar,
        'refs'=>$linkrefs,
        'returnlink'=>"/".$this->lang."/person/".$pid,
        'source'=>"/admin/person/".$pid,
        ]);
    }
    
    
    public function new(Request $request)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $user = $this->getUser();
            $time = new \DateTime();
            $person->setContributor($user->getUsername());
            $person->setUpdateDt($time);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($person);
            $entityManager->flush();
            return $this->redirectToRoute('index');
        }
        
        return $this->render(
            'person/register.html.twig',
            array('form' => $form->createView() , 
            'lang'=>$this->lang,
            'returnlink'=>'person/all',
            )
            );
    }
    
    
    public function edit($pid)
    {
        
        $request = $this->requestStack->getCurrentRequest();
        $user = $this->getUser();
        $time = new \DateTime();
        if($pid>0)
        {
            $person = $this->getDoctrine()->getRepository('AppBundle:Person')->findOne($pid);
        }
        if(! isset($person))
        {
            $person = new Person();
        }
        $form = $this->createForm(PersonForm::class, $person);
        if ($request->getMethod() == 'POST') 
        {
            #$form->bindRequest($request);
            $form->handleRequest($request);
            if ($form->isValid()) 
            {
                $person->setContributor($user->getUsername());
                $person->setUpdateDt($time);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($person);
                $entityManager->flush();
                $pid = $person->getPersonId();
                return $this->redirect("/admin/person/".$pid);
            }
        }
        
        return $this->render('person/edit.html.twig', array(
            'form' => $form->createView(),
            'objid'=>$pid,
            'person'=>$person,
            'returnlink'=>'/admin/person/'.$pid,
            ));
    }
    
    
    
    public function addimage($pid,$iid)
    {
       $lref = $this->getDoctrine()->getRepository('AppBundle:Imageref')->findMatch('person',$pid,$iid);
       if(!$lref)
       {
        $imageref = new Imageref();
        $imageref->setImageid($iid);
        $imageref->setObjecttype("person");
        $imageref->setObjid($pid);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($imageref);
        $entityManager->flush();
        }
        return $this->redirect("/admin/person/".$pid);
    }
    
    
    public function addContent($pid,$cid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $path = "/content/".$cid;
        $lref = $this->getDoctrine()->getRepository('AppBundle:Linkref')->findbyPath('person',$pid,$path);
        if(!$lref)
        {
        $obj = $this->getDoctrine()->getRepository('AppBundle:Content')->findContentLang($cid,$this->lang);
        $label= $obj->getLabel();
        $linkref = new Linkref();
        $linkref->setLabel($label);
        $linkref->setPath($path);
        $linkref->setObjecttype("person");
        $linkref->setObjid($pid);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($linkref);
        $entityManager->flush();
        }
        return $this->redirect("/admin/person/".$pid);
    }
    
    
    public function addBiblo($pid,$bid)
    {
        $path = "/biblo/".$bid;
        $book = $this->getDoctrine()->getRepository('AppBundle:Linkref')->findbyPath('biblo',$bid,$path);
        if(!$book)
        {
        $obj = $this->getDoctrine()->getRepository('AppBundle:Biblo')->findOne($bid);
        $label= $obj->getTitle();
        $linkref = new Linkref();
        $linkref->setLabel($label);
        $linkref->setPath($path);
        $linkref->setObjecttype("person");
        $linkref->setObjid($pid);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($linkref);
        $entityManager->flush();
        }
        return $this->redirect("/admin/person/".$pid);
    }
    
      
    public function addUrl($pid,$uid)
    {
        $path = "/url/".$uid;
        $url = $this->getDoctrine()->getRepository('AppBundle:Linkref')->findbyPath('url',$uid,$path);
        if(!$url)
        {
        $obj = $this->getDoctrine()->getRepository('AppBundle:Url')->findOne($uid);
        $label= $obj->getLabel();
        $linkref = new Linkref();
        $linkref->setLabel($label);
        $linkref->setPath($path);
        $linkref->setObjecttype("person");
        $linkref->setObjid($pid);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($linkref);
        $entityManager->flush();
        }
        return $this->redirect("/admin/person/".$pid);
    }
    
    public function addLocation($pid,$lid)
    {
        $path = "/location/".$lid;
        $lref = $this->getDoctrine()->getRepository('AppBundle:Linkref')->findbyPath('person',$pid,$path);
        if(!$lref)
        {
        $obj = $this->getDoctrine()->getRepository('AppBundle:Location')->findOne($lid);
        $label= $obj->getLabel();
        $linkref = new Linkref();
        $linkref->setLabel($label);
        $linkref->setPath($path);
        $linkref->setObjecttype("person");
        $linkref->setObjid($pid);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($linkref);
        $entityManager->flush();
        }
        return $this->redirect("/admin/person/".$pid);
    }
    
    public function removeimage($pid,$iid)
    {
        $this->getDoctrine()->getRepository("AppBundle:Imageref")->delete('person',$pid,$iid);
        return $this->redirect("/admin/person/".$pid);
    }
    
    
     public function removelink($pid,$lid)
    {
        $this->getDoctrine()->getRepository("AppBundle:Linkref")->deleteOne($lid);
        return $this->redirect("/admin/person/".$pid);
    }
    
    
    public function addevent($pid,$eid)
    {
        $participations =  $this->getDoctrine()->getRepository("AppBundle:Participant")->findParticipationsbyEntityPerson($eid, $pid);
        if(count($participations)<1)
        {
            $user = $this->getUser();
            $time = new \DateTime();
            $part = new Participant();
            $part->setContributor($user->getUsername());
            $part->setUpdateDt($time);
            $part->setEventid((int)$eid);
            $part->setPersonid((int)$pid);
            $part->setNameRecorded(" new name");
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($part);
            $entityManager->flush();
        }
        return $this->redirect("/admin/person/".$pid);
    }
    
    
    public function deleteParticipation($pid,$partid)
    {
        
        $this->getDoctrine()->getRepository("AppBundle:Participant")->deleteOne($partid);
        return $this->redirect("/admin/person/".$pid);
    }
    
    
    public function addbookmark($pid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
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
        return $this->redirect("/admin/person/".$pid);
        
    }
    
     public function delete($pid)
    {
         $person =  $this->getDoctrine()->getRepository("AppBundle:Person")->findOne($pid);
         $mess = "person ". $person->getFullname()." deleted";
         $this->getDoctrine()->getRepository("AppBundle:Linkref")->deleteGroup("person",$pid);
         $this->getDoctrine()->getRepository("AppBundle:Imageref")->deleteGroup("person",$pid);
         $em = $this->getDoctrine()->getManager();
         $em->remove($person);
         $em->flush();
        return $this->redirect('/accueil/message/'.$mess);
        
    }
    
    public function personlist()
    {
        $fp = fopen("docs/roleofhonour.htm","w");
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        

            $people = $this->getDoctrine()->getRepository("AppBundle:Person")->findAll();
       
    
            foreach($people as $person)
            {
                $person->link = "/fr/person/".$person->getId();
                fwrite($fp, "<div class='person' > <a href='".$person->link."'>".$person->getFullname()."</a></div>\n" );
            }
            
            fclose($fp);
         $mess = "person.file.produced";;
           return $this->redirect('/accueil/message/'.$mess);
        
       
    }
}




