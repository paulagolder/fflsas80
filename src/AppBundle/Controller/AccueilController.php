<?php
// src/Controller/AcueilController.php
namespace AppBundle\Controller;


use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;


use AppBundle\Entity\person;
use AppBundle\Entity\event;
use AppBundle\Service\MyLibrary;


class AccueilController  extends Controller
{
    
    private $lang="fr";
    private $mylib;
    private $requestStack ;
    private $translator;
    
    public function __construct( MyLibrary $mylib ,RequestStack $request_stack, TranslatorInterface $translator)
    {
        $this->mylib = $mylib;
        $this->requestStack = $request_stack;
        $this->translator =$translator;
        
    }
    
   


    
    public function showEnglish()
    {
        $this->lang= "EN";
        $this->mylib->setLang("EN");
        $request = new Request();
        $request->setLocale("EN");
        $this->requestStack->getCurrentRequest()->setLocale("en");
        return $this->showall();
    }
    
  
    
    public function showFrench()
    {
        $this->lang= "FR";
        $this->mylib->setLang("FR");
        $request = new Request();
        $request->setLocale("FR");
        $this->requestStack->getCurrentRequest()->setLocale("fr");
        return $this->showall();
    }
    
    public function showall()
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $mess= "";
        $heading= "Welcome";
        $texts = $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup("accueil",1);
        $counts = array();
        
        $counts[0]["label"] ="hommes";
        $counts[0]["number"] =   count($this->getDoctrine()->getRepository("AppBundle:Person")->findAll());
        $counts[1]["label"] ="evenements";
        $counts[1]["number"] =   count($this->getDoctrine()->getRepository("AppBundle:Event")->findAll());
        $counts[2]["label"] ="images";
        $counts[2]["number"] =   count($this->getDoctrine()->getRepository("AppBundle:Image")->findAll());
        
 
        return $this->render('accueil/showall.html.twig', 
        [ 
        'lang' => $this->lang,
        'message' =>  $mess,
        'heading' => $heading,
        'counts'=> $counts, 
        'texts'=> $texts,
        ]);
    }
    
      
    public function showmessage($mess)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
      
        return $this->render('accueil/showmessage.html.twig', 
        [ 
        'lang' => $this->lang,
        'message' =>  $mess,
        'heading' => '',
        ]);
    }
    
     
}
