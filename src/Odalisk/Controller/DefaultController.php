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

    /**
     * deleteSearchPortal.
     */
    public function deleteSearchPortal($portal_id)
    {
        $session = $this->getRequest()->getSession();
        $session->set('search','');

        return $this->redirect($this->generateUrl('portal_details', array('portal_number' => $portal_id)));
    }
}
