<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Table(name="message")
* @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
*/

class Message
{
   /**
    * @var int
    *
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */

   private $id;

     /**
     * @ORM\Column(type="string", length=25)
     */
   private $fromname;

     /**
     * @ORM\Column(type="string", length=100)
     */
   private $fromemail;

     /**
     * @ORM\Column(type="string", length=25)
     */
   private $toname;
   
     /**
     * @ORM\Column(type="string", length=100)
     */
   private $toemail;
    
         /**
     * @ORM\Column(type="string", length=100)
     */
   private $bcc;
    
      /**
     * @ORM\Column(type="string", length=25)
     */
   private $subject;

     /**
     * @ORM\Column(type="text", length=2000, unique=true)
     */
   private $body;
   
   
   /**
   * @ORM\Column(type="datetime", nullable=true)
   */

   private $date_sent;
   
      /**
   * @ORM\Column(type="boolean")
   */

   private $private;

  

   public function getId()

   {
       return $this->id;
   }

   

   public function setToname($name)
   {
       $this->toname = $name;
       return $this;
   }

   

   public function getToname()
   {
       return $this->toname;
   }
   
    public function setFromname($name)
   {
       $this->fromname = $name;
       return $this;
   }

 

   public function getFromname()
   {
       return $this->fromname;
   }

     public function setToemail($to)
   {
       $this->toemail = $to;
       return $this;
   }

 

   public function getToemail()
   {
       return $this->toemail;
   }
   
     public function getBcc()
   {
       return $this->bcc;
   }
   
        public function setBcc($to)
   {
       $this->bcc = $to;
       return $this;
   }


   public function setFromemail($email)
   {
       $this->fromemail = $email;
       return $this;
   }

 
   public function getFromemail()
   {
       return $this->fromemail;
   }
   

  

   public function setSubject($subject)
   {
       $this->subject = $subject;
       return $this;
   }

 

   public function getSubject()
   {
       return $this->subject;
   }



   public function setBody($body)
   {
       $this->body = $body;
       return $this;
   }

 

   public function getBody()

   {
       return $this->body;
   }
   
   public function getDate_sent(): ?\DateTime
   {
     return $this->date_sent;
   } 
   
   public function setDate_sent(?\DateTime $date_sent): self
   {
     $this->date_sent = $date_sent;
      return $this;
  }
  
     public function getPrivate()
   {
       return $this->private;
   }
   
    public function setPrivate($priv)
   {
       $this->private = $priv;
       return $this;
   }

  
  public function __construct($toname, $toemail, $fromname , $fromemail, $subject, $body)
    {
        $this->toname= $toname;
        $this->toemail = $toemail;
        $this->fromname = $fromname;
        $this->fromemail =$fromemail;
        $this->subject = $subject;
        $this->body =$body;
    }
    
     public static function CreateMessage()
    {
        return new Message("","","","","","");
    }
       

}
