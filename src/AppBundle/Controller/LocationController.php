<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;


use AppBundle\Form\LocationForm;

use AppBundle\Service\MyLibrary;
use AppBundle\Entity\location;
use AppBundle\Service\KMLFileUploader;

class LocationController extends Controller
{
    
    
    private $lang="fr";
    private $mylib;
    private $requestStack ;
    
    public function __construct( MyLibrary $mylib ,RequestStack $request_stack)
    {
        $this->mylib = $mylib;
        $this->requestStack = $request_stack;;
    }
    
     
    public function Showall()
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $Locations = $this->getDoctrine()->getRepository("AppBundle:Location")->findAll();
        if (!$Locations) {
            return $this->render('Location/showall.html.twig', 
            [ 
            'lang'=>$this->lang,
            'message' =>  'Locations not Found',
            ]);
        }
        
        return $this->render('Location/showall.html.twig', 
        [ 
        'lang'=>$this->lang,
        'message' =>  '',
        'heading' =>  'all locations('.count($Locations).')',
        'locations'=> $Locations,
        ]);
        
    }
    
    public function Showtop()
    {
        $world  = $this->getDoctrine()->getRepository("AppBundle:Location")->findTop();
        return $this->Showone($world->getLocid());
    }
    
    public function Edittop()
    {
        $world  = $this->getDoctrine()->getRepository("AppBundle:Location")->findTop();
        return $this->Editone($world->getLocid());
    }
    
    
    
    public function Showone($lid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $location = $this->getDoctrine()->getRepository("AppBundle:Location")->findOne($lid);
        if (!$location) 
        {
            return $this->render('location/showone.html.twig', [ 'message' =>  'location '+$lid+' not Found',]);
        }
        
        
        $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("location",$lid);
        
        $parents= $location->ancestors;
        if(count($parents))
        {
            $l = count($parents);
            $z=5;
            for($i=0; $i<$l;$i++)
            {
                $pid = $parents[$i]['id'];
                $url = "/".$this->lang."/location/".$pid;
                $parents[$i]['link'] =  $url ;
                $z++;
            }
            $location->ancestors =$parents;
            if($location->getZoom() <1)
            {
                $location->setZoom($z);
            }
            
        }
        $kml = $location->getKml();
        
        $childrenlist =  $location->children;
        $children = array();
          $childrenkml = "";
        if(count($childrenlist))
        {
          
            $l = count($childrenlist);
            for($i=0; $i<$l;$i++)
            {
                $cid = $childrenlist[$i]['id'];
                $achild = $this->getDoctrine()->getRepository("AppBundle:Location")->findOne($cid);
                if($i>0) $childrenkml .= ",";
                $childrenkml .= $achild->getKml();
                $url = "/".$this->lang."/location/".$cid;
                $achild->setLink($url) ;
                $children[$i] =   $achild ;
            }
           $childrenkml .= "";
            $location->children = $children;
        }
        
        if(isset($text_ar["comment"]) ) $textcomment = $text_ar["comment"];
        else  $textcomment = null;
        
        $map = null;
        $eventlocs=$this->getDoctrine()->getRepository("AppBundle:Event")->findLocations($lid);
        foreach( $eventlocs as $key =>$event )
        {
            $event->link = "/".$this->lang."/event/".$event->getEventid();
        }
        $incidentlocs=$this->getDoctrine()->getRepository("AppBundle:Incident")->findLocations($lid);
        foreach( $incidentlocs as $key =>$incident )
        {
            $incident->link = "/".$this->lang."/person/".$incident->getPersonid();
            $incident->label= $this->getDoctrine()->getRepository("AppBundle:Person")->getLabel($incident->getPersonid());
        }
        return $this->render('location/showone.html.twig', 
        [ 
        'lang'=>$this->lang,
        'message' =>  '',
        'heading' =>  'location '.$lid.' found',
        'location'=> $location,
        'childrenkml' =>    $childrenkml,
        'showchildren' =>  $location->getShowchildren(),
        'texts'=> $textcomment,
        'personlocs'=>$incidentlocs,
        'eventlocs'=>$eventlocs,
        'map'=>$map,              
        ]);
        
    }
    
    public function Editone($lid)
    {
        
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $location = $this->getDoctrine()->getRepository("AppBundle:Location")->findOne($lid);
        if (!$location) 
        {
            return $this->render('location/editone.html.twig', [ 'message' =>  'location '+$lid+' not Found',]);
        }
        
        $kml= $location->getKml();
        $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("location",$lid);
        
        $parents= $location->ancestors;
        $z = 15;
        if(count($parents))
        {
            $l = count($parents);
            
            for($i=0; $i<$l;$i++)
            {
                $pid = $parents[$i]['id'];
                $url = "/admin/location/".$pid;
                $parents[$i]['link'] =  $url ;
                $z--;
            }
            $location->ancestors =$parents;
            
            //echo("ZOOM".$location->zoom );
        }
        if($location->getZoom() <1)
        {
            $location->setZoom( $z);
        }
        
        $childrenlist =  $location->children;
        $children = array();
          $childrenkml = "";
        if(count($childrenlist))
        {
          
            $l = count($childrenlist);
            for($i=0; $i<$l;$i++)
            {
                $cid = $childrenlist[$i]['id'];
                $achild = $this->getDoctrine()->getRepository("AppBundle:Location")->findOne($cid);
                if($i>0) $childrenkml .= ",";
                $childrenkml .= $achild->getKml();
                $url = "/".$this->lang."/location/".$cid;
                $achild->setLink($url) ;
                $children[$i] =   $achild ;
            }
           $childrenkml .= "";
            $location->children = $children;
        }
        
        if(isset($text_ar["comment"]) ) $textcomment = $text_ar["comment"];
        else  $textcomment = null;
        
        $map = null;
        $kml = $location->getKml();
        
        return $this->render('location/editone.html.twig', 
        [ 
        'lang'=>$this->lang,
        'message' =>  '',
        'heading' =>  'location '.$lid.' found',
        'location'=> $location,
        'source'=>'/admin/location/'.$lid,
        'texts'=> $textcomment,
        'map'=>$map,  
        'returnlink'=>"/".$this->lang."/location/".$lid,
        ]);
        
    }
    
    public function edit($lid)
    {
        
        $request = $this->requestStack->getCurrentRequest();
        if($lid>0)
        {
            $location = $this->getDoctrine()->getRepository('AppBundle:Location')->findOne($lid);
            $region = $this->getDoctrine()->getRepository('AppBundle:Location')->findOne($location->getRegion());
        }
        if(! isset($location))
        {
            $location = new Location();
        }
        $form = $this->createForm(LocationForm::class, $location);
        
        if ($request->getMethod() == 'POST') 
        {
            
            $form->handleRequest($request);
            if ($form->isValid()) 
            {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($location);
                $entityManager->flush();
                return $this->redirect("/admin/location/".$lid);
            }
        }
        
        if($region) 
        {
            $regionname = $region->getName();
        } 
        else
        {
            $regionname = "No Parent";
        }
         $childrenlist =  $location->children;
        $children = array();
          $childrenkml = "";
        if(count($childrenlist))
        {
          
            $l = count($childrenlist);
            for($i=0; $i<$l;$i++)
            {
                $cid = $childrenlist[$i]['id'];
                $achild = $this->getDoctrine()->getRepository("AppBundle:Location")->findOne($cid);
                if($i>0) $childrenkml .= ",";
                $childrenkml .= $achild->getKml();
                $url = "/".$this->lang."/location/".$cid;
                $achild->setLink($url) ;
                $children[$i] =   $achild ;
            }
           $childrenkml .= "";
            $location->children = $children;
        }
        return $this->render('location/edit.html.twig', array(
            'form' => $form->createView(),
            'regionname'=>$regionname,
            'location'=>$location,
            'objid'=>$lid,
            'uploadlink'=>'/admin/location/uploadkml/'.$lid,
            'returnlink'=>'/admin/location/'.$lid,
            ));
    }
    
     public function setparent($pid, $lid)
    {
        
        if($lid>0)
        {
            $location = $this->getDoctrine()->getRepository('AppBundle:Location')->findOne($lid);
           if(count($location->children) < 1)
           {
                $location->setRegion($pid);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($location);
                $entityManager->flush();
               
            }
        }
         return $this->redirect("/admin/location/".$pid);
    }

    
    public function new($rid)
    {
        
        $request = $this->requestStack->getCurrentRequest();
        if($rid>0)
        {
            $region = $this->getDoctrine()->getRepository('AppBundle:Location')->findOne($rid);
        }
        
        $location = new location();
        $location->setRegion($rid);
        $location->setLatitude( 46.63874 );
        $location->setLongitude(4.86918);
        
        $form = $this->createForm(LocationForm::class, $location);
        
        if ($request->getMethod() == 'POST') 
        {
            
            $form->handleRequest($request);
            if ($form->isValid()) 
            {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($location);
                $entityManager->flush();
                return $this->redirect("/admin/location/".$rid);
            }
        }
        
        return $this->render('location/edit.html.twig', array(
            'form' => $form->createView(),
            'regionname'=>$region->getName(),
            'location'=>$location,
            'returnlink'=>'/admin/location/'.$rid,
            ));
    }
    
    public function addbookmark($lid)
    {
        $location =  $this->getDoctrine()->getRepository("AppBundle:Location")->findOne($lid);
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $llist = $session->get('locationList');
        if($llist == null)
        {
            $llist = array();
        }
        if( !array_key_exists ( $lid , $llist))
        {
            $newloc = array();
            $newloc['id'] = $lid;
            $newloc["label"]= $location->getName();
            $llist[$lid] = $newloc;
            $session->set('locationList', $llist);
        }
        return $this->redirect('/admin/location/'.$lid);
        
    }
    
    
    public function addUserbookmark($lid)
    {
        $location =  $this->getDoctrine()->getRepository("AppBundle:Location")->findOne($lid);
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $llist = $session->get('locationList');
        if($llist == null)
        {
            $llist = array();
        }
        if( !array_key_exists ( $lid , $llist))
        {
            $newloc = array();
            $newloc['id'] = $lid;
            $newloc["label"]= $location->getName();
            $llist[$lid] = $newloc;
            $session->set('locationList', $llist);
        }
        return $this->redirect("/".$this->lang.'/location/'.$lid);
    }
    
    /**
     * 
     * Method("POST")
     */
    public function upload($lid,Request $request) 
    {
        try {
            $file = $request->files->get ( 'kmlfile' );
            $original_name = $file->getClientOriginalName ();
            $file->move ( $this->container->getParameter ( 'kml-folder-long'), $original_name  );
            # $file_entity = new UploadedFile ();
            #  $file_entity->setFileName ( $fileName );
            #  $file_entity->setActualName ( $original_name );
            #  $file_entity->setCreated ( new \DateTime () );
            $location =  $this->getDoctrine()->getRepository("AppBundle:Location")->findOne($lid);
            $location->setKml("kml/".$original_name);
            $manager = $this->getDoctrine ()->getManager ();
            $manager->persist ( $location);
            $manager->flush ();
            #  $array = array (
            #      'status' => 1,
            #     'file_id' => $file_entity->getFileId () 
            # );
            return $this->redirect('/admin/location/'.$lid);
            
        } catch ( Exception $e ) {
            $array = array('status'=> 0 );
            $response = new JsonResponse($array, 400);
            return $response;
        }
        
        
    }
    
      public function LocationSearch($search, Request $request)
    {
        $message="";
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        if(isset($_GET['searchfield']))
        {
            $pfield = $_GET['searchfield'];
            $this->mylib->setCookieFilter("location",$pfield);
        }
        else
        {
            if(strcmp($search, "=") == 0) 
            {
                $pfield = $this->mylib->getCookieFilter("location");
            }
            else
            {
               $pfield="*";
               $this->mylib->clearCookieFilter("location");
            }
        }
    
        if (is_null($pfield) || $pfield=="" || !$pfield || $pfield=="*") 
        {
            $locations = $this->getDoctrine()->getRepository("AppBundle:Location")->findAll();
            $subheading =  'found.all';
            
        }
        else
        {
            $sfield = "%".$pfield."%";
            $locations= $this->getDoctrine()->getRepository("AppBundle:Location")->findSearch($sfield);
            $subheading =  'found.with';
        }
        
        
        if (count($locations)<1) 
        {
             $subheading = 'nothing.found.for';
        }
        else
        {
            foreach($locations as $location)
            {
                $location->link = "/admin/url/addbookmark/".$location->getLocId();
            }
            
        }
        
        
        return $this->render('location/locationsearch.html.twig', 
        [ 
        'lang'=>$this->lang,
        'message' => $message,
        'heading' =>  'Gestion des Locations',
        'subheading' =>  $subheading,
        'searchfield' =>$pfield,
        'locations'=> $locations,
        
        ]);
    }
    
    
}
