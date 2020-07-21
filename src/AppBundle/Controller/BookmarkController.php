<?php
// src/Controller/BookmarkController.php
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

#use AppBundle\Entity\person;
#use AppBundle\Entity\event;
use AppBundle\Service\MyLibrary;


class BookmarkController  extends Controller
{
    
    private $lang="fr";
    # private $mylib;
    private $requestStack ;
    
    public function __construct( MyLibrary $mylib ,RequestStack $request_stack)
    {
        # $this->mylib = $mylib;
        $this->requestStack = $request_stack;
    }
    
    
    public function delete($blt,$key)
    {
        $request = $this->requestStack->getCurrentRequest();
        $source = $request->query->get('source');
        
        $session = $request->getSession();
        $ilist = $session->get($blt.'List');
        $ilist = array_values($ilist);
        if($ilist != null)
        {
            $k = count($ilist);
            for($j=0; $j<$k; $j++)
            {
                if(array_key_exists($j, $ilist))
                {
                    if($ilist[$j]['id']==$key)
                    {
                        unset($ilist[$j]);
                    }
                }
            }
            
            $session->set($blt.'List', $ilist);
        }
        return $this->redirect("/admin/bookmark/edit");
    }
    
    
    public function setfield($name='', $color='',$blt="all")
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $request = $this->requestStack->getCurrentRequest();
        $source = $request->query->get('source');
        
        if($blt =="all")
        {
            $blts =["image","person","event","location","content","url","biblo"];
            $blists = array();
            foreach( $blts as $blt )
            {
                if($session->get($blt.'List'))
                {
                    $blists[$blt] =  $session->get($blt.'List');
                }
            }
            return $this->render('bookmarks/bookmark.html.twig', 
            [
            'source'=>$source,
            'blists'=>$blists,
            ]);
        }
    }
    
    
    public function Edit()
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $request = $this->requestStack->getCurrentRequest();
        $source = $request->query->get('source');
        
        $blts =["image","person","event","location","content","url","biblo"];
        $blists = array();
        foreach( $blts as $blt )
        {
            $blists[$blt] =  $session->get($blt.'List');
            
        }
        return $this->render('bookmarks/editall.html.twig', 
        [
        'source'=>$source,
        'blists'=>$blists,
        ]);
    }
    
    
}
