<?php

namespace AppBundle\Controller;

//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\SwiftmailerBundle\Swift_SmtpTransport;
use Symfony\Component\Translation\TranslatorInterface;
#use AppBundle\MyClasses\EMail;

use AppBundle\Entity\Message;
use AppBundle\Service\MyLibrary;
use AppBundle\Form\MessageForm;
use AppBundle\Form\UserMessageForm;
use AppBundle\Form\VisitorMessageForm;


use Symfony\Component\HttpFoundation\RequestStack;

class MessageController extends Controller
{ 
    
    
    private $requestStack ;
    private $lang="fr";
    private $mylib;
    private $trans;
    
    
    public function __construct( MyLibrary $mylib,RequestStack $request_stack,TranslatorInterface $translator )
    {
        $this->mylib = $mylib;
        $this->requestStack = $request_stack;
        $this->trans =$translator;
        
    }
    
    public function createMessageToAdmin(Request $request ,\Swift_Mailer $mailer) 
    {
        $user = $this->getUser();
        if($user)
        {
            return $this->createUserMessageToAdmin($request ,$mailer)  ;
        }
        else
        { 
            return $this->createVisitorMessageToAdmin($request ,$mailer)  ;
        }
    }
    
    public function createUserMessageToAdmin(Request $request ,\Swift_Mailer $mailer) 
    {
        $user = $this->getUser();
        $message = new Message($this->getParameter('admin-name'), $this->getParameter('admin-email'),$user->getUsername(),$user->getEmail() ,"", ""); 
        $form = $this->createForm(UserMessageForm::class, $message);
        $form->handleRequest($request);
        if($form->isSubmitted() &&  $form->isValid())
        {
            $this->sendMessageToUserCopytoAdministrators($message,$user->getUserid(), $user->getLang());
            return $this->render('message/usermessage.html.twig',array(
                'message'=>$message,
                'returnlink' =>"/$this->lang/person/all")
                );                
        } ;
        
        return $this->render('message/userform.html.twig', array( 
        'lang'=>$this->lang,
        'form' => $form->createView(),  
        ));
    }
    
    public function createVisitorMessageToAdmin(Request $request ,\Swift_Mailer $mailer) 
    {
        $lang = $this->requestStack->getCurrentRequest()->getLocale();
        $message = new Message($this->getParameter('admin-name'), $this->getParameter('admin-email'),"", "" ,"", "");
        $form = $this->createForm(VisitorMessageForm::class, $message);
        $form->handleRequest($request);
        if($form->isSubmitted() &&  $form->isValid()  && $this->captchaverify($request->get('g-recaptcha-response')))
        {
            $this->sendMessageToUserCopytoAdministrators($message,0, $lang);
            return $this->render('message/usermessage.html.twig',array(
                'message'=>$message,
                'returnlink' =>"/$this->lang/person/all")
                );                
        } 
        return $this->render('message/visitorform.html.twig', array( 
        'lang'=>$this->lang,
        'form' => $form->createView(),  
        ));
    }
    
    public function showMessages()
    {
        $messages =  $this->getDoctrine()->getRepository("AppBundle:Message")->findAdmin();
        
        return $this->render('message/showall.html.twig', array( 'lang'=>$this->lang,
        'messages' => $messages,
        'returnlink'=> "/admin/message/all",
        ));
    }
    
    
    public function showAdminMessage($cid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        
        
        $message = $this->getDoctrine()->getRepository('AppBundle:Message')->find($cid);
        #$user =  $this->getDoctrine()->getRepository('AppBundle:User')->findbyEmail($user->getEmail());
        
        return $this->render('message/show.html.twig', array(
            'lang'=>$this->lang,
            'message' =>$message,
            'returnlink'=> "/admin/message/all",
            ));
    }
    
    public function showUserMessage($uid,$cid)
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        
        
        $message = $this->getDoctrine()->getRepository('AppBundle:Message')->find($cid);
        #$user =  $this->getDoctrine()->getRepository('AppBundle:User')->findbyEmail($user->getEmail());
        
        return $this->render('message/show.html.twig', array(
            'lang'=>$this->lang,
            'message' =>$message,
            'returnlink'=> "/admin/user/".$uid,
            ));
    }
    
    public function deleteMessage($cid)
    {
        
        $this->getDoctrine()->getRepository('AppBundle:Message')->delete($cid);
        return $this->redirect("/admin/message/all");
        
    }
    
    public function makeMessageToUser($uid,Request $request ,\Swift_Mailer $mailer) 
    {
        $fuser = $this->getDoctrine()->getRepository('AppBundle:User')->findOne($uid);
        
        $subject ="";
        $body="";
        $message = new message($fuser->getUsername(),$fuser->getEmail(),$this->getParameter('admin-name'), $this->getParameter('admin-email'),$subject, $body);
        
        $form = $this->createForm(MessageForm::class, $message);
        $form->handleRequest($request);
        
        if($form->isSubmitted() &&  $form->isValid())
        {
            $this->sendMessageToUser($message,$fuser->getUserid(), $fuser->getLang());
            
            return $this->render('message/admintousermessage_ack.html.twig',array(
                'message'=>$message,
                'returnlink'=>'/admin/user/'.$uid,
                ));          
        } ;
        
        return $this->render('message/sendto.html.twig', array( 'lang'=>$this->lang,
        'form' => $form->createView()  
        ));
    }
    
    public function makeBulkMessage(int $sid) 
    {
        $this->lang = $this->requestStack->getCurrentRequest()->getLocale();
        $content =  $this->getDoctrine()->getRepository('AppBundle:Content')->findContentlang($sid, $this->lang);
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        $destinataires = $session->get('selectedusers');
        $userlist = explode(",",$destinataires);
        $numbertosend= count($userlist) - 1;
        return $this->render('message/bulkmessage.html.twig', array(
            'lang'=>$this->lang,
            'content'=>$content,
            'destinataires' =>$destinataires,
            'numbertosend'=>$numbertosend,
            'returnlink'=>'/admin/user/search',
            'actionlink'=>'/admin/message/bulk/send/'.$sid,
            ));
    }
    
    public function sendBulkMessage(int $sid,Request $request ,\Swift_Mailer $mailer) 
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        $destinataires = $session->get('selectedusers');
        $userlist = explode(",",$destinataires);
        $sendtoemailstr= "";
        foreach($userlist as $uid)
        {
            $user =  $this->getDoctrine()->getRepository('AppBundle:User')->findOne($uid);
            if($user)
            {
                $lang =  $user->getLang();
                
                $content =  $this->getDoctrine()->getRepository('AppBundle:Content')->findContentLang($sid,$lang);
                $subject = $content->gettitle();
                $body= $content->gettext();
                $sendtoemailstr .= $user->getemail().", ";
                $sendtoname = "group.email";
                $message = new message($user->getUserName(),$user->getEmail(),$this->getParameter('admin-name'), $this->getParameter('admin-email'),$subject, $body);
                $datesent =new \DateTime();
                $message->setDate_sent( $datesent);
                $footer =  $this->renderView('message/template/'.$lang.'/contentemailfooter.html.twig',array('userid'=> $uid ,'subjectid'=>$sid ,),'text/html');
                $userbody =    $this->renderView('message/template/'.$lang.'/emailfull.html.twig',array(
                    'message'=>$message,'footer'=>$footer,),'text/html');
                    
                    $smessage = $this->makeSwiftMessage($message, $userbody);
                    $this->get('mailer')->send($smessage);
                    
            }
        }
        $sn = $this->getDoctrine()->getManager();   
        $message->setBcc( $sendtoemailstr);
        $sn -> persist($message);
        $sn -> flush();
        // $this->get('mailer')->send($smessage);
    
    
    return $this->render('message/bulkusermessage_ack.html.twig',array(
        'message'=>$message,
        'returnlink'=>'/admin/user/search',
        ));          
        
}





function makeSwiftMessage($message,$formattedbody)
{
    $smessage = (new \Swift_Message('FFLSAS Email'));
    $smessage->setSubject($message->getSubject());
    $sender = $this->getParameter('admin-email');
    $sendername = $this->getParameter('admin-name');
    $smessage->setFrom($sender,$sendername);
    $smessage->setTo($message->getToEmail());
    $smessage->setBody($formattedbody);
    $smessage->setContentType("text/html");
    return $smessage;
    
}

function makeSwiftMessageCopyToAdministrators($message,$formattedbody)
{
    $smessage = (new \Swift_Message('FFLSAS Email'));
    $smessage->setSubject($message->getSubject());
    $sender = $this->getParameter('admin-email');
    $administrators = explode(",",$this->getParameter('administratorsemails'));
    $sendername = $this->getParameter('admin-name');
    $smessage->setFrom($sender,$sendername);
    $smessage->setTo($administrators);
    $smessage->setBody($formattedbody);
    $smessage->setContentType("text/html");
    return $smessage;
    
}

function sendMessage($message)
{
    $datesent =new \DateTime();
    $message->setDate_sent( $datesent);
    $sn = $this->getDoctrine()->getManager();      
    $sn -> persist($message);
    $sn -> flush();
    $formattedbody =    $this->renderView('message/emailbody.html.twig',array('message'=>$message,),'text/html');
    $smessage = $this->makeSwiftMessage($message, $formattedbody);
    $this->get('mailer')->send($smessage);
} 

function sendMessageToUserCopytoAdministrators($message,$userid,$lang)
{
    $datesent =new \DateTime();
    $message->setDate_sent( $datesent);
    $sn = $this->getDoctrine()->getManager();      
    $sn -> persist($message);
    $sn -> flush();;
    if($userid==0)
    {
        $footer =  $this->renderView('message/template/'.$lang.'/visitoremailfooter.html.twig',array(),'text/html');
    }
    else
    {
        $footer =  $this->renderView('message/template/'.$lang.'/useremailfooter.html.twig',array('userid'=> $userid ),'text/html');
        
    }
    $userbody =    $this->renderView('message/template/'.$lang.'/emailfull.html.twig',array(
        'message'=>$message,'footer'=>$footer,),'text/html');       
        $umessage = $this->makeSwiftMessage($message, $userbody);
        $this->get('mailer')->send($umessage);
        $adminfooter =  $this->renderView('message/template/'.$lang.'/adminemailfooter.html.twig',array(),'text/html');
        $adminbody =    $this->renderView('message/template/'.$lang.'/emailfull.html.twig',array(
            'message'=>$message, 'footer'=>$adminfooter,),'text/html');
            $amessage = $this->makeSwiftMessageCopyToAdministrators($message,$adminbody);
            $this->get('mailer')->send($amessage);
} 


function sendMessageToAdmin($message,$lang)
{
    $datesent =new \DateTime();
    $message->setDate_sent( $datesent);
    $sn = $this->getDoctrine()->getManager();      
    $sn -> persist($message);
    $sn -> flush();
    $adminfooter =  $this->renderView('message/template/'.$lang.'/adminemailfooter.html.twig',array(),'text/html');
    $formattedbody =    $this->renderView('message/template/'.$lang.'/emailfull.html.twig',array(
        'message'=>$message,'footer'=>$adminfooter,),'text/html');
        
        $smessage = $this->makeSwiftMessage($message, $formattedbody);
        $this->get('mailer')->send($smessage);
} 

function sendMessageToUser($message,$userid,$lang)
{
    $datesent =new \DateTime();
    $message->setDate_sent( $datesent);
    $sn = $this->getDoctrine()->getManager();      
    $sn -> persist($message);
    $sn -> flush();
    $userfooter =  $this->renderView('message/template/'.$lang.'/useremailfooter.html.twig',
           array('userid'=>$userid,),'text/html');
    $formattedbody =    $this->renderView('message/template/'.$lang.'/emailfull.html.twig',
           array('message'=>$message,'footer'=>$userfooter,),'text/html');
    $smessage = $this->makeSwiftMessage($message, $formattedbody);
    $this->get('mailer')->send($smessage);
} 

function sendConfidentialMessageToUser($message,$userid,$lang)
{
    $datesent =new \DateTime();
    $message->setDate_sent( $datesent);
    $message->setPrivate(TRUE);
    $sn = $this->getDoctrine()->getManager();      
    $sn -> persist($message);
    $sn -> flush();
    $userfooter =  $this->renderView('message/template/'.$lang.'/useremailfooter.html.twig',array('userid'=>$userid,),'text/html');
    $formattedbody = $this->renderView('message/template/'.$lang.'/emailfull.html.twig',
                  array('message'=>$message,'footer'=>$userfooter,),'text/html');
    $smessage = $this->makeSwiftMessage($message, $formattedbody);
    $this->get('mailer')->send($smessage);
} 

function sendUserMessage($subjecttag,$bodytag,$user)
{
     $adminlang = $this->requestStack->getCurrentRequest()->getLocale();
     $body =  $this->renderView('message/template/'.$user->getLang().'/'.$bodytag.'.html.twig',array('user'=> $user));
     $subject = $this->trans->trans($subjecttag,[],"messages",$user->getLang());
     $message = new message($user->getUsername(),$user->getEmail(),$this->getParameter('admin-name'), $this->getParameter('admin-email'),$subject, $body);
     if(substr_compare($message->getToEmail(),".free.fr", -strlen(".free.fr")) === 0)
     {
       $message->setSubject ( "REDIRECTED+".$message->getSubject());
       $sentmessage = $this->get('message_service')->sendMessageToUser($message,$user->getUserid(), $user->getLang());
     }
     else
     {
   
      $sentmessage = $this->get('message_service')->sendMessageToAdmin($message, $adminlang);
     }
}

function sendConfidentialUserMessage($subjecttag,$bodytag,$user)
{
     $body =  $this->renderView('message/template/'.$user->getLang().'/'.$bodytag.'.html.twig',array('user'=> $user));
       $subject = $this->trans->trans($subjecttag,[],"messages",$user->getLang());
     $message = new message($user->getUsername(),$user->getEmail(),$this->getParameter('admin-name'), $this->getParameter('admin-email'),$subject, $body);
     $sentmessage = $this->get('message_service')->sendConfidentialMessageToUser($message,$user->getUserid(), $user->getLang());
}

function sendAdminMessage($subjecttag,$bodytag,$user,$lang)
{
      $abody =  $this->renderView('message/template/'.$lang.'/'.$bodytag.'.html.twig',array('user'=> $user));
      $subject = $this->trans->trans($subjecttag,[],"messages",$lang);
      $amessage = new message($user->getUsername(),$user->getEmail(),$this->getParameter('admin-name'), $this->getParameter('admin-email'),$subject, $abody);
      $asmessage = $this->get('message_service')->sendMessageToAdmin($amessage, $lang);
}



  


function captchaverify($recaptcha)
{
    $secret = $this->container->getParameter('recaptcha_secret');
    $url = "https://www.google.com/recaptcha/api/siteverify";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        "secret"=>$secret,"response"=>$recaptcha));
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response);     
        
        return $data->success;   
        //return true;
}
}
