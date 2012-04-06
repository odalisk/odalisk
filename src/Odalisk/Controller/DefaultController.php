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
        
        return array(
            'name' => 'Julien Sanchez',
            'maintenance_status' => $this->container->getParameter('app.maintenance'),
        );
    }
}
