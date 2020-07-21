<?php

// src/Controller/SecurityController.php

namespace AppBundle\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;

use AppBundle\Service\MyLibrary;


class SecurityController extends Controller
{

    private $lang="fr";
    private $mylib;
    private $requestStack ;
    
    public function __construct( MyLibrary $mylib ,RequestStack $request_stack)
    {
        $this->mylib = $mylib;
        $this->requestStack = $request_stack;;
    }

   
public function loginAction(Request $request, AuthenticationUtils $authenticationUtils)
{
      $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
    // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();
    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();
    return $this->render('security/login.html.twig', array(
        'last_username' => $lastUsername,
        'error'         => $error,
        'lang' =>"FR",
    ));
}


    public function loginCheckAction()
    {
          $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
          return $this->redirect('/accueil/message/'." Login check action");
    }


    public function logoutAction()
    {
        //do whatever you want here 
        //clear the token, cancel session and redirect
       ## $this->get('security.context')->setToken(null);
       ## $this->get('request')->getSession()->invalidate();
        return $this->redirect($this->generateUrl('login'));
    }

    
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
{
    // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();

    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();
    return $this->render('security/login.html.twig', array(
        'last_username' => $lastUsername,
        'error'         => $error,
    ));
}


  


}
