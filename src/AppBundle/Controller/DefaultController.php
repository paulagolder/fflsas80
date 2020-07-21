<?php
// src/Controller/DefaultController.php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function hello()
    {
        return $this->redirect("/fr/accueil");
    }
}
