<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;

use AppBundle\Entity\event;
use AppBundle\Entity\Text;
use AppBundle\Entity\person;
use AppBundle\Service\MyLibrary;
use AppBundle\Form\TextForm;
use AppBundle\Form\TextForm_server;


class TextController extends Controller
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
        return $this->render('text/index.html.twig', [
        'controller_name' => 'TextController',
        ]);
    }
    
    
    
    public function Showall()
    {
        $Texts = $this->getDoctrine()->getRepository("AppBundle:Text")->findAll();
        if (!$Texts) {
            return $this->render('text/showall.html.twig', [ 'message' =>  'Texts not Found',]);
        }
        
        return $this->render('text/showall.html.twig', [ 'message' =>  '','heading' =>  'all Texts ('.count($Texts).')','texts'=> $Texts,]);
        
    }
    
    
    public function Showgroup()
    {
        $Texts = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("person",674);
        if (!$Texts) {
            return $this->render('text/showall.html.twig', [ 'message' =>  'Texts not Found',]);
        }
        return $this->render('text/showall.html.twig', [ 'message' =>  '','heading' =>  'all Texts ('.count($Texts).')','texts'=> $Texts,]);
        
    }
    
    public function editperson($pid)
    {
        $person = $this->getDoctrine()->getRepository("AppBundle:Person")->findOne($pid);
        $personname = $person->getFullname();
        $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("person",$pid);
        
        
        return $this->render('text/editdetail.html.twig', [ 
        'message' =>  '',
        'heading' =>  'edit.texts',
        'label' => $personname,
        'texts'=> $text_ar,
        'attributes'=> ['comment'],
        'objecttype'=>'person',
        'objid'=>$pid,
        'returnlink'=>"/admin/person/".$pid,
        ]);
        
    }
    
    public function editevent($eid)
    {
        $event = $this->getDoctrine()->getRepository("AppBundle:Event")->findOne($eid);
        $label = $event->getLabel();
        $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("event",$eid);
        
        return $this->render('text/editdetail.html.twig', [ 
        'message' =>  '',
        'heading' =>  'edit.texts',
        'label' => $label,
        'texts'=> $text_ar,
        'attributes'=> ['title','comment'],
        'objecttype'=>'event',
        'objid'=>$eid,
        'returnlink'=>"/admin/event/".$eid,
        ]);
        
    }
    
    
    public function editimage($iid)
    {
        $image = $this->getDoctrine()->getRepository("AppBundle:Image")->findOne($iid);
        $imagename = $image->getName();
        $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("image",$iid);
        
        return $this->render('text/editdetail.html.twig', [ 
        'message' =>  '',
        'heading' =>  'edit.texts',
        'label' => $imagename,
        'texts'=> $text_ar,
        'attributes'=> ['title','comment'],
        'objecttype'=>'image',
        'objid'=>$iid,
        'returnlink'=>"/admin/image/".$iid,
        ]);
        
    }
    
    
    public function editreflink($rfid)
    {
        $ref = $this->getDoctrine()->getRepository("AppBundle:reflink")->findOne($rfid);
        $refname = $ref->getName();
        $text_ar = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("reflink",$rfid);
        
        return $this->render('text/editdetail.html.twig', [ 
        'message' =>  '','heading' =>  'edit.texts',
        'label' => $refname,
        'texts'=> $text_ar,
        'attributes'=> ['title','comment'],
        'objecttype'=>'image',
        'objid'=>$rfid,
        'returnlink'=>"/admin/reflink/".$rfid,
        ]);
        
    }
    
    
    
    public function editone($objecttype,$objid,$attribute, $language)
    {   
        
        $language = strtoupper($language);
        switch ($objecttype) 
        {
            case "person":
                $person = $this->getDoctrine()->getRepository('AppBundle:Person')->findOne($objid);
                $label = $person->getFullname();
                break;
            case "event":
                $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOne($objid);
                $label = $event->getLabel();
                break;
            case "image":
                $image = $this->getDoctrine()->getRepository('AppBundle:Image')->findOne($objid);
                $label = $image->getName();
                break;
            case "linkref":
                $ref = $this->getDoctrine()->getRepository('AppBundle:Linkref')->findOne($objid);
                $label = $ref->getLabel();
                break;
            default:
                return $this->redirect("/admin/text/".$objecttype."/".$objid);
        }
        
        
        $request = $this->requestStack->getCurrentRequest();
        $text = $this->getDoctrine()->getRepository('AppBundle:Text')->findOne($objecttype, $objid,$attribute,$language);
        if($text==null)
        {
            $text = new Text();
            $text->setObjecttype($objecttype);
            $text->setObjid($objid);
            $text->setAttribute($attribute);
            $text->setLanguage($language);
        }
        $text->setContributor($this->getUser()->getUsername());
        $now = new \DateTime();
        $text->setUpdateDt($now);
        
        $form = $this->createForm(TextForm::class, $text);
        
        if ($request->getMethod() == 'POST') {
            #$form->bindRequest($request);
            $form->handleRequest($request);
            if ($form->isValid())
            {
                $text->setComment ( $this->filterText($text->getComment()));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($text);
                $entityManager->flush();
                return $this->redirect("/admin/text/".$objecttype."/".$objid);
                
            }
        }
        
        return $this->render('text/update.html.twig', array(
            
            'form' => $form->createView(),
            'label'=> $label,
            'attribute'=> $attribute,
            'language' => $language,
            'objecttype'=>$objecttype,
            'objid'=> $objid,
            'returnlink' => "/admin/text/".$objecttype."/".$objid,
            ));
    }
    
    
    public function edit($tid)
    {   
        $text = $this->getDoctrine()->getRepository('AppBundle:Text')->findOne($tid);
        $objecttype = $text->getObjecttype();
        $objid = $text->getObjid();
        $text->setContributor($this->getUser()->getUsername());
        $now = new \DateTime();
        $text->setUpdateDt($now);
        
        $form = $this->createForm(TextForm_server::class, $text);
        $request = $this->requestStack->getCurrentRequest();
        if ($request->getMethod() == 'POST') {
            #$form->bindRequest($request);
            $form->handleRequest($request);
            if ($form->isValid())
            {
                $text->setComment ( $this->filterText($text->getComment()));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($text);
                $entityManager->flush();
                return $this->redirect("/admin/text/".$objecttype."/".$objid);
                
            }
        }
        
        return $this->render('text/update.html.twig', array(
            
            'form' => $form->createView(),
            'text' =>$text,
            'returnlink' => "/admin/".$objecttype."/".$objid,
            ));
    }
    
    
    public function edit_ct($tid)
    {   
        $text = $this->getDoctrine()->getRepository('AppBundle:Text')->findOne($tid);
        $objecttype = $text->getObjecttype();
        $objid = $text->getObjid();
        $text->setContributor($this->getUser()->getUsername());
        $now = new \DateTime();
        $text->setUpdateDt($now);
        
        $form = $this->createForm(TextForm_server::class, $text);
        $request = $this->requestStack->getCurrentRequest();
        if ($request->getMethod() == 'POST') {
            #$form->bindRequest($request);
            $form->handleRequest($request);
            if ($form->isValid())
            {
                $text->setComment ( $this->filterText($text->getComment()));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($text);
                $entityManager->flush();
                return $this->redirect("/admin/text/".$objecttype."/".$objid);
                
            }
        }
        
        return $this->render('text/update.html.twig', array(
            
            'form' => $form->createView(),
            'text' =>$text,
            'returnlink' => "/admin/text/".$objecttype."/".$objid,
            ));
    }
    
    
    public function new($objecttype,$objid,$attribute,$language)
    {   
        $text = new Text();
        $text ->setLanguage($language);
        $text ->setObjid($objid);
        $text ->setAttribute($attribute);
        $text ->setObjecttype($objecttype);
        $text ->setComment("?");
        $text->setContributor($this->getUser()->getUsername());
        $now = new \DateTime();
        $text ->setUpdateDt($now);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($text);
        $entityManager->flush();
        $tid = $text->getId();
        return $this->redirect("/admin/text/".$tid);
    }
    
    public function filterText($text)
    {
        
        $text = preg_replace('/(<p.+?)style=".+?"(>.+?)/i', "$1$2", $text);
        $text = preg_replace('/(<p.+?)class=".+?"(>.+?)/i', "$1$2", $text);
        $text = preg_replace('/(<span.+?)style=".+?"(>.+?)/i', "$1$2", $text);
        $text =  strip_tags($text,"<p><img><br>");
        
        return $text;
    }
    
    public function edit_quill($tid)
    {   
        $request = $this->requestStack->getCurrentRequest();
        
        $text= $this->getDoctrine()->getRepository('AppBundle:Text')->findOne($tid);
        $objecttype = $text->getObjecttype();
        $objid = $text->getObjid();
        $sourcelabel = $this->get('alibrary_service')->getLabel($objecttype,$objid, $this->lang);
        
        $text->setContributor($this->getUser()->getUsername());
        $now = new \DateTime();
        $text->setUpdateDt($now);
     
        
        
        return $this->render('text/edit_quill.html.twig', array(
            'source'=>  $sourcelabel,
            'text' =>$text,
            'returnlink' => "/admin/text/".$objecttype."/".$objid,
            
            ));
    }
    
    public function process_edit($tid)
    {   
        $request = $this->requestStack->getCurrentRequest();
        $text= $this->getDoctrine()->getRepository('AppBundle:Text')->findOne($tid);
        $objecttype = $text->getObjecttype();
        $objid = $text->getObjid();
        $attribute = $text->getAttribute();
        $lang= $text->getLanguage(); 
        
        $text ->setContributor($this->getUser()->getUsername());
        $now = new \DateTime();
        $text ->setUpdateDt($now);
        
        
        if ($request->getMethod() == 'POST') 
        {
            $comment =$request->request->get('_text');
            if($attribute =="title")
            {
                $text->setComment(strip_tags($comment));
            }
            else
                $text->setComment($this->filterText($comment));
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($text);
            $entityManager->flush();
            return $this->redirect("/admin/".$objecttype."/".$objid);
        }
        
        return $this->render('text/edit_quill.html.twig', array(
            
            'text'=> $text,
            'returnlink' => "/admin/text/".$text->getId(),
            'contentid'=>$textid,
            ));
    }
    
    
    public function delete($tid)
    {   
        $text = $this->getDoctrine()->getRepository('AppBundle:Text')->findOne($tid);
        $objecttype = $text->getObjecttype();
        $objid = $text->getObjid();
        
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($text);
        $entityManager->flush();
        return $this->redirect("/admin/text/".$objecttype."/".$objid);
        
    }
}
