<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\File;


use AppBundle\Entity\Content;
use AppBundle\Entity\Text;
use AppBundle\Service\MyLibrary;
use AppBundle\Form\ContentForm;

class ContentController extends Controller
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
        return $this->render('content/index.html.twig', [
        'controller_name' => 'ContentController',
        ]);
    }
    
    public function ShowCatchAll()
    {
        $def = $this->container->getParameter('defaultcontent');
        return $this->ShowContent($def);
        
    }
    
    public function ShowContent($sid=201)
    {
        return $this->ShowContentLang($sid,$this->lang);
    }
    
    public function ShowContentLang($sid,$lang)
    {
        $content=null;
        $content_ar = $this->getDoctrine()->getRepository("AppBundle:Content")->findSubject($sid);
        if(!$content_ar )
        {
            return $this->render('content/showone.html.twig', 
            [
            'message' =>  'contenu non trouver '.$sid,
            'lang'=>$lang,
            'content'=> null,
            'refs'=> null,
            ]);
        }
        if(array_key_exists ($lang,$content_ar )) 
        {
            $content = $content_ar[$lang] ;
        }
        elseif(array_key_exists ("fr",$content_ar ))
        {
            $content = $content_ar['fr'] ;
        }
        elseif(array_key_exists ("en",$content_ar ))
        {
            $content = $content_ar['en'] ;
        }
        else
        {
            #dump($content_ar);=
            $content = $content_ar['*'] ;
        }
        $text = $content->getText();
        $text = $this->insertInsertions($text);
        $content->setText( $text);
        $cid = $content->getContentId();
        $linkrefs = $this->get('linkref_service')->getLinks("subject",$sid, $this->lang);
        $langlist = array();
        foreach($content_ar as $lcont)
        {
            
            $langlist[$lcont->getLanguage()]=$lcont->getSubjectId();
            
        }
        return $this->render('content/showone.html.twig', 
        [
        'message' =>  '',
        'lang'=>$this->lang,
        'langlist' =>$langlist,
        'content'=> $content,
        'refs'=>$linkrefs,
        ]);
    }
    
    public function xShowcontent($cid,$lang)
    {
        $content=null;
        $content= $this->getDoctrine()->getRepository("AppBundle:Content")->findOne($cid);
        if(is_null($content))
        {
            
            return $this->render('content/showone.html.twig',
            [
            'message' =>  'content not found ',
            'lang'=>$this->lang,
            'content'=> '',
            'title'=>'',
            'refs'=>'',
            ]);
            
        }
        $content->setText( $this->insertInsertions($content->getText()));
        $text_ar =  $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup('content',$cid);
        
        $linkrefs = $this->get('linkref_service')->getLinks("subject",$content->getSubjectid(), $this->lang);
        return $this->render('content/showone.html.twig', 
        [
        'message' =>  '',
        'lang'=>$this->lang,
        'altlang'=>"27",
        'content'=> $content,
        
        'refs'=>$linkrefs,
        ]);
    }
    
    public function Editone($cid)
    {
        $content = $this->getDoctrine()->getRepository("AppBundle:Content")->findOne($cid);
        $text = $content->getText();
        $text = $this->insertImages($text);
        //  $content->setText(  $this->cleanText($text));
        #$text_ar =  $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup('content',$cid);
        $title = $this->mylib->selectText($text_ar,'title',$this->lang);
        $comment =  $this->mylib->selectText($text_ar,'comment',$this->lang);
        
        
        return $this->render('content/editone.html.twig', 
        ['lang'=>$this->lang, 
        'message' =>  '',
        'content'=> $content,
        'objid' => $cid,
        'title'=>$title,
        'comment' => $comment,
        'refs' => null,
        'returnlink'=>'/admin/content/search',
        ]);
    }
    
    public function Editcontent($sid)
    {
        $contents = $this->getDoctrine()->getRepository("AppBundle:Content")->findSubject($sid);
        foreach ($contents as $content)
        {
            $text_ar =  $this->getDoctrine()->getRepository("AppBundle:Text")->findGroup('content',$content->getContentid());
            $title = $this->mylib->selectText($text_ar,'title',$this->lang);
            if($title)
                $content->setLabel($title);
            else
                $content->setLabel($content->getTitle());
        }
        $linkrefs = $this->get('linkref_service')->getLinksfrom("content",$sid);
        
        return $this->render('content/editcontent.html.twig', 
        ['lang'=>$this->lang, 
        'message' =>  '',
        'contents'=> $contents,
        'subjectid' => $sid,
        'refs' => $linkrefs,
        'returnlink'=>'/admin/content/search',
        ]);
    }
    
    public function edit_quill($cid)
    {   
        $request = $this->requestStack->getCurrentRequest();
        $contentid=$cid;
        $content= $this->getDoctrine()->getRepository('AppBundle:Content')->findOne($contentid);
        if($content == null) return $this->redirect("/admin/content/search");
        //  $label = $content->getTitle();
        // $sid = $content->getSubjectid();
        
        $content ->setContributor($this->getUser()->getUsername());
        $now = new \DateTime();
        $content ->setUpdateDt($now);
        $content->setText($this->cleanText($content->getText()));
        
        
        return $this->render('content/edit_quill.html.twig', array(
            'content' =>$content,
            'returnlink' => "/admin/content/".$content->getsubjectid(),
            
            ));
    }
    
    public function process_edit($cid)
    {   
        $request = $this->requestStack->getCurrentRequest();
        $contentid=$cid;
        $content= $this->getDoctrine()->getRepository('AppBundle:Content')->findOne($contentid);
        $sid = $content->getSubjectid();
        
        if ($request->getMethod() == 'POST') 
        {
            $content->setTitle($request->request->get('_title'));
            $content->setAccess($request->request->get('_access'));
            $content->setTags($request->request->get('_tags'));
            // $request->request->get('_tags'))
            $content->setText($this->cleanText( $request->request->get('_text')));
            //  $content->setText($this->cleanText($content->getText()));
            $content ->setContributor($this->getUser()->getUsername());
            $now = new \DateTime();
            $content ->setUpdateDt($now);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($content);
            $entityManager->flush();
            
            return $this->redirect("/admin/content/".$sid);
            
        }
        
        return $this->render('content/edit.html.twig', array(
            'form' => $form->createView(),
            'label'=> $label,
            'returnlink' => "/admin/content/".$content->getsubjectid(),
            'contentid'=>$contentid,
            ));
    }
    
    
    public function edit($cid)
    {   
        $request = $this->requestStack->getCurrentRequest();
        $contentid=$cid;
        $content= $this->getDoctrine()->getRepository('AppBundle:Content')->findOne($contentid);
        $label = $content->getTitle();
        $sid = $content->getSubjectid();
        
        $content ->setContributor($this->getUser()->getUsername());
        $now = new \DateTime();
        $content ->setUpdateDt($now);
        $content->setText($this->cleanText($content->getText()));
        $form = $this->createForm(ContentForm::class, $content);
        if ($request->getMethod() == 'POST') 
        {
            $form->handleRequest($request);
            if ($form->isValid()) {
                # $content->setText($this->cleanText($content->getText()));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($content);
                $entityManager->flush();
                return $this->redirect("/".$this->lang."/content/".$sid);
                
            }
        }
        
        #$matches = array();
        
        # $n = preg_match_all('(<img\s[A-z="]*\s*src[^"]"[^"]+[^/>]+/>)', $content->getText(),$matches);
        
        return $this->render('content/edit.html.twig', array(
            'form' => $form->createView(),
            'label'=> $label,
            'returnlink' => "/admin/content/".$content->getsubjectid(),
            'contentid'=>$contentid,
            ));
    }
    
    
    public function newContentLang($sid,$lang)
    {   
        $content = new Content();
        $content->setLanguage($lang);
        $content->setSubjectid($sid);
        $content->setTitle("?");
        $content->setText("?");
        $content->setAccess("2");
        $content ->setContributor($this->getUser()->getUsername());
        $now = new \DateTime();
        $content ->setUpdateDt($now);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($content);
        $entityManager->flush();
        $cid = $content->getContentid();
        return $this->redirect("/admin/content/edit/".$cid);
    }
    
    public function newContent()
    {   
        $sid = $this->newSubjectId();
        return $this->EditContent($sid);
    }
    
    public function Showall(Request $request)
    {
        
        $message="";
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        
        $pfield = $request->query->get('searchfield');
        $gfield = $request->query->get('searchfield');
        
        if (!$pfield) 
        {
            $contents = $this->getDoctrine()->getRepository("AppBundle:Content")->findAll();
            $heading =  'found.all';
            
        }
        else
        {
            $pfield = "%".$pfield."%";
            $contents = $this->getDoctrine()->getRepository("AppBundle:Content")->findSearch($pfield);
            $heading =  'found.with';
        }
        
        
        if (count($contents)<1) 
        {
            $subheading = 'nothing.found.for';
        }
        else
        {
            
        }
        
        return $this->render('content/showall.html.twig', 
        [ 
        'lang'=> $this->lang,
        'message' => $message,
        'heading' =>  $heading,
        'searchfield' =>$gfield,
        'contents'=> $contents,
        
        ]);
    }
    
    public function ShowNews()
    {
        
        $message="";
        $subjects = array();
        $latest=null;
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $contents = $this->getDoctrine()->getRepository("AppBundle:Content")->findNews();
        if (count($contents)<1) 
        {
            $message = 'no.news';
        }
        else
        {
            
            foreach($contents  as $content)
            {
                $sid = $content->getSubjectId();
                if (!in_array($sid, $subjects)) 
                {
                    
                    $subjects[]= $sid;
                }
            }
            $contents = array();
            foreach($subjects as $subjectid)
            {
              $content  =  $this->getDoctrine()->getRepository("AppBundle:Content")->findContentLang($subjectid,$this->lang);
              $text = $content->getText();
              $text = $this->insertInsertions($text);
              $content->setText( $text);
              $contents[] = $content;
            }
        }
        return $this->render('content/shownews.html.twig', 
        [ 
        'lang'=> $this->lang,
        'message' => $message,
        'contents'=> $contents,        
        ]);
    }
    
    public function ShowNewsMenu()
    {
        
        $message="";
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $contents = $this->getDoctrine()->getRepository("AppBundle:Content")->findNews();
        if (count($contents)<1) 
        {
            return $this->render('dummy.html.twig');
        }
        
        else{
            
            return $this->render('content/newsmenuitem.html.twig');
        }
    }
    
    
    public function ContentSearch($search,Request $request)
    {
        $message="";
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        if(isset($_GET['searchfield']))
        {
            $pfield = $_GET['searchfield'];
            $this->mylib->setCookieFilter("content",$pfield);
        }
        else
        {
            if(strcmp($search, "=") == 0) 
            {
                $pfield = $this->mylib->getCookieFilter("content");
            }
            else
            {
                $pfield="*";
                $this->mylib->clearCookieFilter("content");
            }
        }
        
        
        if (is_null($pfield) || $pfield=="" || !$pfield || $pfield=="*") 
        {
            $contents = $this->getDoctrine()->getRepository("AppBundle:Content")->findAll();
            $subheading =  'found.all';
            
        }
        else
        {
            $sfield = "%".$pfield."%";
            $contents = $this->getDoctrine()->getRepository("AppBundle:Content")->findSearch($sfield);
            $subheading =  'found.with';
        }
        
        
        if (count($contents)<1) 
        {
            $subheading = 'nothing.found.for';
        }
        else
        {
            foreach($contents as $content)
            {
                $content->link = "/admin/content/addbookmark/".$content->getContentid();
            }
            
        }
        
        
        return $this->render('content/contentsearch.html.twig', 
        [ 
        'lang'=>$this->lang,
        'message' => $message,
        'heading' =>  'Gestion des Articles',
        'subheading' =>  $subheading,
        'searchfield' =>$pfield,
        'contents'=> $contents,
        
        ]);
    }
    
    
    public function addBookmark($sid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $content =  $this->getDoctrine()->getRepository("AppBundle:Content")->findContentLang($sid,$this->lang);
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $ilist = $session->get('contentList');
        if($ilist == null)
        {
            $ilist = array();
        }
        $newcontent = array();
        $newcontent['id'] = $sid;
        $newcontent["label"]= $content->getTitle();
        $ilist[$sid]= $newcontent;
        $session->set('contentList', $ilist);
        
        return $this->redirect("/admin/content/search");
    }
    
    public function addUserBookmark($sid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        
        $content =  $this->getDoctrine()->getRepository("AppBundle:Content")->findContentLang($sid,$this->lang);
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $ilist = $session->get('contentList');
        if($ilist == null)
        {
            $ilist = array();
        }
        $newcontent = array();
        $newcontent['id'] = $sid;
        $newcontent["label"]= $content->getTitle();
        $ilist[$sid]= $newcontent;
        $session->set('contentList', $ilist);
        
        return $this->redirect("/".$this->lang."/content/".$sid);
    }
    
    
    public function Deleteimage($cid,$isn)
    {
        $contentid=$cid;
        $content= $this->getDoctrine()->getRepository('AppBundle:Content')->findOne($contentid);
        $matches = array();
        $n = preg_match_all('(<img\s[A-z="]*\s*src[^"]"[^"]+[^/>]+/>)', $content->getText(),$matches);
        
        $text = $content->getText();
        $searchtext = $matches[0][$isn];
        $newtext = str_replace( $searchtext, "IMAGE NON TROUVEE",$text);
        $content->setText($newtext);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($content);
        $entityManager->flush();
        
        return $this->redirect("/admin/content/edit/".$cid);
    }
    
    public function Delete($cid)
    {
        $content= $this->getDoctrine()->getRepository('AppBundle:Content')->findOne($cid);
        $this->getDoctrine()->getRepository("AppBundle:Content")->delete($cid);
        return $this->redirect("/admin/content/edit/".$content->getSubjectid());
    }
    
    
    public function insertInsertions($text)
    {
        $k1 = strpos ( $text , "[[" );
        while($k1 >0 )
        {
            $k2 = strpos ( $text , "]]",$k1 );
            $tokengroup = substr($text,$k1, $k2-$k1+2);
            $tokens=substr($tokengroup,2,$k2-$k1-2);
            $token_list=json_decode("{".$tokens."}",true);
            $obj = array_keys($token_list)[0];
            if($obj=="image")
            {
                $replacementstring = $this->imageinsert($token_list);
                $text = str_replace ($tokengroup , $replacementstring , $text );   
            }
            elseif($obj=="url")
            {
                $replacementstring = $this->urlinsert($token_list);
                $text = str_replace ($tokengroup , $replacementstring , $text );   
            }
            elseif($obj=="content")
            {
                $replacementstring = $this->contentinsert($token_list);
                $text = str_replace ($tokengroup , $replacementstring , $text );   
            }
            else
            {
                $replacementstring = "<div>NOT FOUND </div>";
                $text = str_replace ($tokengroup , $replacementstring , $text );   
            }
            $k1 = strpos ( $text , "[[" );   
        }
        return $text;
    }
    
    
    public function imageinsert($token_list)
    {
        
        $imageid =  $token_list['image'];
        $image =  $this->getDoctrine()->getRepository("AppBundle:Image")->findOne($imageid);
        if($image)
        {
            $this->mylib->setFullpath($image);
            $style="";
            if(array_key_exists ('width' , $token_list))
            {
                $style .= "width:".$token_list['width'].";";
            }
            if(strlen($style)>0 )
                $inlinestyle = " style=\"".$style."\" ";
            else
                $inlinestyle="";
            $replacementstring =  "<img src='".$image->getFullPath()."'".$inlinestyle.">" ;
        }
        else  
            $replacementstring = "<div>NO IMAGE </div>";
        return $replacementstring;    
    }
    
    public function urlinsert($token_list)
    {
        $urlid =  $token_list['url'];
        $url =  $this->getDoctrine()->getRepository("AppBundle:Url")->findOne($urlid);
        if($url)
        {
            $label= $url->getLabel();
            if(array_key_exists ('label' , $token_list))
            {
                if($token_list['label']!="")
                {
                    $label= $token_list['label'];
                }
            }
            $replacementstring = "<a href='".$url->getUrl()."' target=/'_blank/' >".$label."</a>" ;
        }
        else  
            $replacementstring = "<div>NO URL </div>" ;
        return $replacementstring;   
    }
    
    public function contentinsert($token_list)
    {
        $contentid =  $token_list['content'];
        $content =  $this->getDoctrine()->getRepository("AppBundle:Content")->findOne($contentid);
        if($content)
        {
            $label= $content->getLabel();
            
            $replacementstring = "<a href='/fr/content/".$contentid."' target=/'_blank/' >".$label."</a>" ;
        }
        else  
            $replacementstring = "<div>NO content </div>" ;
        return $replacementstring;   
    }
    
    
    
    public function cleanText($text)
    {
        
        // $text= str_ireplace( "ql-align-center","text-align-center", $text);
        $text= preg_replace('/(\*\*.+?)style=".+?"(\*\*.+?)/i', "", $text);
        $text= preg_replace('/(<p.+?)style=".+?"(>.+?)/i', "$1$2", $text);
        //  $text= preg_replace('/(<p.+?)class=".+?"(>.+?)/i', "$1$2", $text);
        $text= preg_replace('/(<span.+?)style=".+?"(>.+?)/i', "$1$2", $text);
        $text= strip_tags($text,"<p><img><a><br><h1><b><i><h2><strong><em><u><ol><li><ul>");
        $text= str_ireplace("http://fflsas.org/images/stories/fflsas/", "/oldimages/",$text);
        $text= str_ireplace("\"images/stories/fflsas/images/","\"/oldimages/images/", $text);
        $text= str_ireplace("\"images/stories/fflsas/newimages/","\"/newimages/", $text);
        $text= str_ireplace("\"http://www.lerot.net/safedocs/images/","\"/oldimages/images/", $text);
        $text= str_ireplace("\"http://www.lerot.net/fflsasdocs/images/","\"/oldimages/images/", $text);
        $text= str_ireplace("\"http://lerot.org/joomla/images/stories/fflsas/images/","\"/oldimages/images/", $text);
        $text= str_ireplace("\"http://lerot.org/joomla/images/stories/fflsas/newimages/","\"/oldimages/images/", $text);
        $text= str_ireplace("<p>&nbsp;</p>","<p></p>", $text);
        $text= str_ireplace("<p><br></p>","<p></p>", $text);
        $text= str_ireplace("<p></p><p></p>","<p></p>", $text);
        return $text;
    }
    
    public function newSubjectID()
    {
        $osida = $this->getDoctrine()->getRepository("AppBundle:Content")->findMaxSid();
        $osidint = $osida[0][1];
        return $osidint + 1;
    }
}
