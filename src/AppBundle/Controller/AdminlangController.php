<?php

// src/Controller/AdminlangController.php


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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Translation\Loader\ArrayLoader;

use AppBundle\Service\MyLibrary;

class AdminlangController extends Controller
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
   
    public function Changelang($oldpath)
    {
     # $oldpath = str_replace( "_~", "\\",$oldpath);
       # $this->lang= "EN";
       # $this->mylib->setLang("EN");
       # $request = new Request();
      #  $request->setLocale("EN");
       # $this->requestStack->getCurrentRequest()->setLocale("en");
        
      #  $routes = $this->get('router')->getRouteCollection();
       # $newroute =  $routes->get($oldpath);//->getDefaults()['_controller'];
  
       # return new Response(
     #       '<html><body>'.$oldpath.'</body></html>'
      #  );
           return $this->redirectToRoute($oldpath);
    }
}
