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
        
        $repository = $this->getDoctrine()
            ->getRepository('Odalisk\Entity\Portal');
        $portals = $repository->findAll();
        
        return $this->render('App:Portal:index.html.twig', array(
            'maintenance_status' => $this->container->getParameter('app.maintenance'),
            'page_number' => $page_number,
            'portals' => $portals));
    }
    
    /**
     * details.
     *
     * @return array
     */
    public function details($portal_number, $_format)
    {
        // put action your code here
        
        $repository = $this->getDoctrine()
            ->getRepository('Odalisk\Entity\Portal');
        $portal = $repository->findById($portal_number);
        $portal = $portal[0];
        return $this->render('App:Portal:details.html.twig', array(
            'maintenance_status' => $this->container->getParameter('app.maintenance'),
            'portal' => $portal));
    }
}
