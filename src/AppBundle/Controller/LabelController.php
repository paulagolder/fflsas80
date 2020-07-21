<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
#use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
#use Symfony\Component\Security\Core\Encoder;
use Symfony\Component\HttpFoundation\RedirectResponse;


use AppBundle\Entity\Label;

use AppBundle\Form\LabelForm;
use AppBundle\Service\MyLibrary;


class LabelController extends Controller
{
    
    private $lang="fr";
    private $mylib;
    private $requestStack ;
    
    
    
    
    public function __construct( MyLibrary $mylib ,RequestStack $request_stack)
    {
        $this->mylib = $mylib;
        $this->requestStack = $request_stack;
    }
    
    
    
    public function Delete($lid)
    {
            $label = $this->getDoctrine()->getRepository('AppBundle:Label')->findOne($lid);
          $this->getDoctrine()->getRepository("AppBundle:Label")->deletebytag($label->gettag(),$label->getMode());
        return $this->redirect("/admin/label/search");
    }
    
    public function show($tag)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
       
        $labels= $this->getDoctrine()->getRepository("AppBundle:Label")->findByTags($tag);
        if(!labels)
        {
            $mess = "tags not found";
        } else
        {
            $mess = '';
        }
        return $this->render('label/show.html.twig', 
        [ 
        'lang' => $this->lang,
        'message' =>  $mess ,
        'heading' => 'tags.found',
        'tags'=> $tags,
        ]);
    }
    
    
    public function edit($lid)
    {
        $request = $this->requestStack->getCurrentRequest();
        $linkref=null;
        if($lid>0)
        {
            $label = $this->getDoctrine()->getRepository('AppBundle:Label')->findOne($lid);
            $nlab =  $request->query->get("t".$lid);
            if($nlab=="")
            {
            
            }
            else
            {
            $label->setText( $this->formatlabels($nlab));
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($label);
            $entityManager->flush();
            $lid = $label->getId();
            }
            return $this->redirect('/admin/label/search');
        }    
    }
    
    
    public function newtag()
    {
        $request = $this->requestStack->getCurrentRequest();
        $entityManager = $this->getDoctrine()->getManager();
        $nlab =  $request->query->get("newtag");
        $tag = $this->formattag($nlab);
        if($tag =="")    return $this->redirect('/admin/label/search');
        $labelf= new Label();
        $labelf->setTag($tag);
        $labelf->setLang("fr");
        $labelf->setMode("message");
        $labelf->setText("_".$tag);
        $labele= new Label();
        $labele->setTag($tag);
        $labele->setLang("en");
        $labele->setMode("message");
        $labele->setText("_".$tag);
        $entityManager->persist($labelf);
        
        $entityManager->persist($labele);
        $entityManager->flush();
        return $this->redirect('/admin/label/search');
    }
    
    public function LabelSearch($search, Request $request)
    {
        $message="";
        if(isset($_GET['_mode']))
        {
          $mode = $_GET['_mode'];
          $this->mylib->setCookieFilter("mode",$mode);
        }
        else
        {
            $mode=$this->mylib->getCookieFilter("mode"); 
        }
        if($mode=="" )
            $mode="message";
        if(isset($_GET['searchfield']))
        {
            $pfield = $_GET['searchfield'];
            $this->mylib->setCookieFilter("label",$pfield);
        }
        else
        {
            if(strcmp($search, "=") == 0) 
            {
                $pfield = $this->mylib->getCookieFilter("label");
                 $mode=$this->mylib->getCookieFilter("mode"); 
            }
            else
            {
                $pfield="*";
                $this->mylib->clearCookieFilter("label");
                
            }
        }
        if (is_null($pfield) || $pfield=="" || !$pfield || $pfield=="*") 
        {
            $labels = $this->getDoctrine()->getRepository("AppBundle:Label")->findmode($mode);
            $subheading =  'found.all';
        }
        else
        {
            $sfield = "%".$pfield."%";
            $labels = $this->getDoctrine()->getRepository("AppBundle:Label")->findSearch($sfield,$mode);
            $subheading =  'found.with';
        }
        
        if (count($labels)<1) 
        {
            $subheading = 'nothing.found.for';
        }
        else
        {
            foreach($labels as $label)
            {
                
            }
            
        }
          
         if($mode=="" )
            $mode="message";
        return $this->render('label/labelsearch.html.twig', 
        [ 
        'lang'=>$this->lang,
        'message' => $message,
        'heading' =>  'Gestion des Libelles',
        'subheading' =>  $subheading,
        'searchfield' =>$pfield,
        'labels'=> $labels,
        'mode'=>$mode,
        
        ]);
    }
    
    public function generate($mode)
    {
        
        
        $fpfr = fopen("../translations/".$mode."s.fr.yml","w");
        $fpen = fopen("../translations/".$mode."s.en.yml","w");
        
        
        $labels = $this->getDoctrine()->getRepository("AppBundle:Label")->findMode($mode);
        
        
        foreach($labels as $label)
        {
            $outlabel = $this->formatlabels($label->getText());
            if($label->getLang() == "en")
            {
                fwrite($fpen, $label->getTag().": \"". $outlabel."\"\n");
            }
            elseif($label->getLang() == "fr")
            {
                fwrite($fpfr, $label->getTag().": \"". $outlabel."\"\n");
            }
        }
        fclose($fpen);
        fclose($fpfr);
        $mess = "translation.files.produced";
        return $this->redirect('/accueil/message/'.$mess);
        
        
    }
    
    private function formatlabels($intext)
    {
        $text = trim($intext);
        $text =  strip_tags($text);
        $text =  preg_replace("/^'/", '', $text);
        $text =  preg_replace('/^"/', '', $text);
        $text =  rtrim($text, "'");
        $text =  rtrim($text, '"');
        return $text;
    }
    
    private function formattag($intext)
    {
        $text = trim($intext);
        $text =  strip_tags($text);
        $text =  preg_replace("/^'/", '', $text);
        $text =  preg_replace('/^"/', '', $text);
        $text =  rtrim($text, "'");
        $text =  rtrim($text, '"');
        $text =  str_replace('  ', ' ', $text);
        $text =  str_replace(' ', '.', $text);
        $text =  str_replace('..', '.', $text);
         if(strpos($text,'.') !== false) return $text;
         else
         {
             $text = '.'.$text;
         }
         return $text;
    }
}
