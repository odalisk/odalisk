<?php

namespace Odalisk\Controller;

use Knp\Bundle\RadBundle\Controller\Controller;

/**
 * Default controller.
 */
class DefaultController extends Controller
{
    /**
     * index.
     *
     * @return array
     */
    public function index()
    {
        // put action your code here
        if(TRUE == $this->container->getParameter('app.maintenance'))
        {
            return $this->render('App:Default:maintenance.html.twig');
        }
        
        
        return array('name' => 'Julien Sanchez');
    }
}
