<?php 
// src/AppBundle/TwigExtension/customFilters.php 
 
namespace AppBundle\TwigExtension; 
use Twig\TwigFilter; 
 
class customFilters extends \Twig_Extension 
{ 
 
    public function getFilters() 
    { 
    return array( 
            new TwigFilter('date_translate', array($this, 'date_translate')), 
          
        );
    }
 
    public function date_translate($input)
    {
       return "albert:".$input;
       }
 
   
 
}

