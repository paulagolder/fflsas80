<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
#use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
#use Symfony\Component\Security\Core\Encoder;
use Symfony\Component\HttpFoundation\RedirectResponse;


use AppBundle\Entity\Url;

use AppBundle\Form\UrlForm;
use AppBundle\Service\MyLibrary;


class UrlController extends Controller
{
    
    private $lang="fr";
    private $mylib;
    private $requestStack ;
    

    
    
    public function __construct( MyLibrary $mylib ,RequestStack $request_stack)
    {
        $this->mylib = $mylib;
        $this->requestStack = $request_stack;
    }
    
    public function Showall()
    { 
        $urlgroups = array();
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $urls = $this->getDoctrine()->getRepository("AppBundle:Url")->findAll();
        if (!$urls) {
            return $this->render('url/showall.html.twig', [ 'message' =>  'Urls not Found',]);
        }        
        foreach($urls as $url)
        {
           $tag = $url->getTags();
           if(! array_key_exists($tag,$urlgroups))
           {
            $urlgroups[$tag] = array();
           }
           $urlgroups[$tag][$url->getId()]=$url;
            #$fuser->link = "/admin/url/".$fuser->getId();
        }
        return $this->render('url/showall.html.twig', 
        [ 
        'lang' => $this->lang,
        'message' =>  '' ,
        'heading' => 'Browsing urls',
        'groups'=> $urlgroups,
        ]);
    }
    
     public function Delete($urlid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $this->getDoctrine()->getRepository("AppBundle:Url")->delete($urlid);
        $this->getDoctrine()->getRepository('AppBundle:Text')->deleteTexts('url',$urlid);
        $this->getDoctrine()->getRepository('AppBundle:Linkref')->deleteAllLinks('url',$urlid);
         return $this->redirect("/admin/url/search");
    }
    
     public function show($urlid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $url = $this->getDoctrine()->getRepository("AppBundle:Url")->findOne($urlid);
        if(!$url)
        {
          $mess = "URL not found";
        } else
        {
          $mess = '';
        }
        return $this->render('url/show.html.twig', 
        [ 
        'lang' => $this->lang,
        'message' =>  $mess ,
        'heading' => 'Visit.urls',
        'url'=> $url,
        ]);
    }
    
     public function visit($urlid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $url = $this->getDoctrine()->getRepository("AppBundle:Url")->findOne($urlid);
    
        return $this->render('url/show.html.twig', 
        [ 
        'lang' => $this->lang,
        'message' =>  '' ,
        'heading' => 'Browsing urls',
        'url'=> $url,
        ]);
    }
    
      public function edit($urlid)
    {
         $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        
        $request = $this->requestStack->getCurrentRequest();
        $linkref=null;
        if($urlid>0)
        {
            $url = $this->getDoctrine()->getRepository('AppBundle:Url')->findOne($urlid);
        }
        if(! isset($url))
        {
            $url = new Url();
            $url->setId(0);
        }
       
        $form = $this->createForm(UrlForm::class, $url);

        if ($request->getMethod() == 'POST') 
        {
            #$form->bindRequest($request);
            $form->handleRequest($request);
            if ($form->isValid()) 
            {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($url);
                $entityManager->flush();
                $urlid = $url->getId();
                return $this->redirect('/admin/url/edit/'.$urlid );
                
            }
        }
        
        return $this->render('url/edit.html.twig', array(
            'form' => $form->createView(),
            'message' =>  '',
            'url' => $url,
            'returnlink'=>'/'.$this->lang.'/url/show/'.$urlid   ///admin/linkref/event/272/8
            ));
    }
    
     public function UrlSearch(Request $request)
    {
        $message="";
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        
        $pfield = $request->query->get('searchfield');
        $gfield = $request->query->get('searchfield');
        
        if (!$pfield) 
        {
            $urls = $this->getDoctrine()->getRepository("AppBundle:Url")->findAll();
            $subheading =  'found.all';
            
        }
        else
        {
            $pfield = "%".$pfield."%";
            $urls = $this->getDoctrine()->getRepository("AppBundle:Url")->findSearch($pfield);
            $subheading =  'found.with';
        }
        
        
        if (count($urls)<1) 
        {
             $subheading = 'nothing.found.for';
        }
        else
        {
            foreach($urls as $url)
            {
                $url->link = "/admin/url/addbookmark/".$url->getId();
            }
            
        }
        
        
        return $this->render('url/urlsearch.html.twig', 
        [ 
        'lang'=>$this->lang,
        'message' => $message,
        'heading' =>  'manage.links',
        'subheading' =>  $subheading,
        'searchfield' =>$gfield,
        'urls'=> $urls,
        
        ]);
    }
    
    public function addBookmark($uid)
    {
        //$gfield = $request->query->get('searchfield');
        $url =  $this->getDoctrine()->getRepository("AppBundle:Url")->findOne($uid);
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $ilist = $session->get('urlList');
        if($ilist == null)
        {
            $ilist = array();
        }
        $newurl = array();
        $newurl['id'] = $uid;
        $newurl["label"]= $url->getLabel();
        $ilist[$uid]= $newurl;
        $session->set('urlList', $ilist);
          return $this->redirect("/admin/url/search");
        return $this->redirect("/admin/url/search?searchfield=".$gfield);
    }
    
    public function addUserBookmark($uid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        
        $url =  $this->getDoctrine()->getRepository("AppBundle:Url")->findOne($uid);
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $ilist = $session->get('urlList');
        if($ilist == null)
        {
            $ilist = array();
        }
        $newurl = array();
        $newurl['id'] = $uid;
        $newurl["label"]= $url->getLabel();
        $ilist[$uid]= $newurl;
        $session->set('urlList', $ilist);
        
        // return $this->redirect($uri);
        
        return $this->redirect("/".$this->lang."/url/show");
        
    }
    
}
