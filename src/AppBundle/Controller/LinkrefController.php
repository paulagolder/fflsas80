<?php

namespace AppBundle\Controller;

use AppBundle\Service\MyLibrary;

use AppBundle\Entity\Linkref;
use AppBundle\Entity\event;
use AppBundle\Form\LinkrefForm;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;

class LinkrefController extends Controller
{
    
    
    private $lang="fr";
    private $mylib;
    private $requestStack ;
    
    public function __construct( MyLibrary $mylib ,RequestStack $request_stack)
    {
        $this->mylib = $mylib;
        $this->requestStack = $request_stack;
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
    }
    
    
    public function index()
    {
        return $this->render('linkref/index.html.twig', [
        'controller_name' => 'LinkrefController',
        ]);
    }
    
    public function Showall()
    {
        $linkrefs = $this->getDoctrine()->getRepository("AppBundle:Linkref")->findAll();
        
        if (!$linkrefs) {
            return $this->render('linkref/showall.html.twig', [ 'message' =>  'Refs not Found',]);
        }
        
        
        return $this->render('linkref/showall.html.twig', [ 'message' =>  '' ,'heading' =>  'the.links','refs'=> $linkrefs,]);
    }
    
    public function Showone($rid)
    {
        $ref = $this->getDoctrine()->getRepository("AppBundle:Linkref")->findOne($rid);
        if (!$ref) 
        {
            return $this->render('linkref/showone.html.twig', [ 'message' =>  'Ref'.$rid.' not Found',]);
        }
        $path = $ref->getPath();
        if( substr($path, 0,7)== "content")
        {
            $pp = explode("/", $path);
            $cid =$pp[1];
            echo("conetent:".$cid);
            $content=  $this->getDoctrine()->getRepository("AppBundle:Content")->findOne($cid);
            return $this->render('linkref/showone.html.twig', 
            [ 'lang'=>$this->lang, 
            'message' =>  '',
            'content'=>$content,
            'objid'=>$ref->getObjid(),
            'refs'=>null,
            ]);
            
        }
        
        else
        {
            
            $text_ar =  $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup('linkrefs',$rid);
            
            
            return $this->render('linkref/showone.html.twig', 
            ['lang'=>$this->lang, 
            'message' =>  '',
            'texts'=>$text_ar,
            'display'=>"fred",
            'ref'=>$ref
            ]);
            
        }
    }
    
    
    public function EditPersonGroup($pid)
    {
        $refs = $this->getDoctrine()->getRepository("AppBundle:Linkref")->findGroup('person',$pid);
        $person = $this->getDoctrine()->getRepository("AppBundle:Person")->findOne($pid);
        $texts_ar = array();
        foreach( $refs as $ref)
        {
            $texts_ar[$ref['linkid']] =  $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup('linkref',$ref['linkid']);
        }
        
        return $this->render('linkref/editgroup.html.twig', 
        ['lang'=>$this->lang, 
        'message' =>  '',
        'label' => $person->getFullname(),
        'objecttype'=>'person',
        'objid'=>$pid,
        'texts'=>$texts_ar,
        'refs'=>$refs,
        'returnlink'=>"/admin/person/".$pid,
        ]);
    }
    
    public function EditEventGroup($eid)
    {
        $refs = $this->getDoctrine()->getRepository("AppBundle:Linkref")->findGroup('event',$eid);
        
        $texts_ar = array();
        foreach( $refs as $ref)
        {
            $texts_ar[$ref['linkid']] =  $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup('linkrefs',$ref['linkid']);
            
        }
        
        
        return $this->render('linkref/editgroup.html.twig', 
        ['lang'=>$this->lang, 
        'message' =>  '',
        'heading' => "all links for event".$eid,
        'label' => "event:".$eid,
        'objecttype'=>'event',
        'objid'=>$eid,
        'xtexts'=>$texts_ar,
        'refs'=>$refs,
        'returnlink'=>"/admin/event/".$eid,
        ]);
    }
    
    public function Editone($ot,$oid,$lrid)
    {
        $ref = $this->getDoctrine()->getRepository('AppBundle:Linkref')->findOne($lrid);
        $texts_ar =  $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup2('linkref',$lrid);
        return $this->render('linkref/editone.html.twig', 
        ['lang'=>$this->lang, 
        'message' =>  '',
        'heading' => "link id= ".$lrid. " for ".$ot." id=".$oid,
        'objecttype'=>$ot,
        'objid'=>$oid,
        'texts'=>$texts_ar,
        'ref'=>$ref,
        'returnlink'=>"/admin/linkref/edit".$ot."/".$oid,
        ]);
    }
    
     public function Edit($lrid)
    {
        $ref = $this->getDoctrine()->getRepository('AppBundle:Linkref')->findOne($lrid);
        $ot= $ref->getObjecttype();
        $oid = $ref->getObjid();
        $texts_ar =  $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup2('linkref',$lrid);
        return $this->render('linkref/editone.html.twig', 
        ['lang'=>$this->lang, 
        'message' =>  '',
        'heading' => "link id= ".$lrid. " for ".$ot." id=".$oid,
        'objecttype'=>$ot,
        'objid'=>$oid,
        'texts'=>$texts_ar,
        'ref'=>$ref,
        'returnlink'=>"/admin/linkref/edit".$ot."/".$oid,
        ]);
    }
    
    public function Addlink($otype1,$oid1,$otype2,$oid2)
    {
        // $obj = $this->getDoctrine()->getRepository('AppBundle:$otype2')->findOne($oid2);
        // $label= $obj->getLabel();
        $user = $this->getUser();
        $time = new \DateTime();
        $time->setTimestamp($time->getTimestamp());
        $linkref = new Linkref();
        $linkref->setObjecttype($otype1);
        $linkref-> setObjid($oid1);
        $linkref-> setLabel($otype2.":".$oid2);
        $linkref->setPath($otype2."/".$oid2);
        $linkref->setContributor($user->getUsername());
        $now = new \DateTime();
        $linkref->setUpdateDt($now);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($linkref);
        $entityManager->flush();
        $lrid = $linkref->getLinkId();
        
        
        return $this->redirect('/admin/'.$otype1."/".$oid1);
        
    }
    
    
    public function editdetail($ot,$oid,$lrid)
    {
        $user = $this->getUser();
        $time = new \DateTime();
        $time->setTimestamp($time->getTimestamp());
        
        $request = $this->requestStack->getCurrentRequest();
        $linkref=null;
        if($lrid>0)
        {
            $linkref = $this->getDoctrine()->getRepository('AppBundle:Linkref')->findOne($lrid);
        }
        if(! isset($linkref))
        {
            $linkref = new Linkref();
            $linkref->setObjecttype($ot);
            $linkref-> setObjid($oid);
            $linkref->setContributor($user->getUsername());
            $now = new \DateTime();
            $linkref->setUpdateDt($now);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($linkref);
            $entityManager->flush();
            $lrid = $linkref->getLinkId();
        }
        $entity = ucfirst($ot);
        $form = $this->createForm(LinkrefForm::class, $linkref);
        $object =  $this->getDoctrine()->getRepository('AppBundle:'.$entity)->findOne($oid);
        $label = $object->getlabel();
        if ($request->getMethod() == 'POST') 
        {
            #$form->bindRequest($request);
            $form->handleRequest($request);
            if ($form->isValid()) 
            {
                // perform some action, such as save the object to the database
                $linkref->setContributor($user->getUsername());
                $now = new \DateTime();
                $linkref->setUpdateDt($now);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($linkref);
                $entityManager->flush();
                return $this->redirect('/admin/linkref/'.$ot."/".$oid. "/".$lrid );
                
            }
        }
        
        return $this->render('linkref/edit.html.twig', array(
            'form' => $form->createView(),
            'message' =>  '',
            'label' => $label,
            'objid'=>$oid,
            'ref' => $linkref,
            'returnlink'=>'/admin/linkref/'.$ot."/".$oid. "/".$lrid   ///admin/linkref/event/272/8
            ));
    }
    
    
    public function delete($ot,$oid,$lrid)
    {
        $this->getDoctrine()->getRepository("AppBundle:Linkref")->deleteOne($lrid);
        return $this->redirect("/admin/linkref/edit".$ot."/".$oid);
    }
    
    public function getLinks($objecttype, $objectid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $links = $this->getDoctrine()->getRepository("AppBundle:Linkref")->findGroup($objecttype,$objectid);
        $link_ar = array();
        foreach( $links as $link)
        {
            $linkid = $link['linkid'];
            $linkref = array();
            $linkref["id"]= $linkid;
            $label = $link['label'];
            $texts_ar =  $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup('linkref', $linkid);
            if($texts_ar)
            {
            $label  = $this->mylib->selectText($texts_ar,'title',$this->lang);
            }
            $path = $link['path'];
            if(substr( $path, 0,4 ) === "http")
            {
              
            }
            else
            {
                if (substr( $path, 0, 1 ) === "/")
                {
                    $path =  substr($path,1);
                }
                list($objtype, $objid) = explode("/",$path);
                if($objtype =="content")
                {
                    $content = $this->getDoctrine()->getRepository("AppBundle:Content")->findContentLang( $objid,$this->lang);
                    $label = $content->getTitle();
                }
                else
                {
                $texts_ar =  $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup($objtype, $objid);
                if($texts_ar)
                {
                  $label = $this->mylib->selectText($texts_ar,'title',$this->lang);
                }
                }
                $path = "/".$this->lang."/".$path;
            }
            $linkref['path'] = $path;
            if($label )
            {
                $linkref['label'] =    $label ;
            }
            else
            {
                 $linkref['label'] =    $path ;
            }
            
            
            $link_ar[$linkid] = $linkref;
        }
        
        
        return $link_ar;
    }
    
       public function getLinksFrom($objecttype, $objectid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $links = $this->getDoctrine()->getRepository("AppBundle:Linkref")->findbyTarget($objecttype,$objectid);
  
        return $links;
    }
    
    
    
}
