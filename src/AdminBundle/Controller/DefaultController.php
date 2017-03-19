<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/admin", name="AdminBundle:homepage")
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig', [
            'page_title' => 'Statistics'
        ]);
    }
}
