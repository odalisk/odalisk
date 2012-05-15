<?php

namespace Odalisk\Controller;

use Knp\Bundle\RadBundle\Controller\Controller;

/**
 * Portal controller.
 */
class PortalController extends Controller
{
    /**
     * index.
     *
     * @return array
     */
    public function index($page_number, $_format)
    {
        // put action your code here
        
        return array(
            'name' => 'Julien Sanchez',
            'maintenance_status' => $this->container->getParameter('app.maintenance'),
        );
    }
}
