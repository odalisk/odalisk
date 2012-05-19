<?php

namespace Odalisk\Controller;

use Knp\Bundle\RadBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Portal controller.
 */
class PortalController extends Controller
{
    static private $page_size = 20;

    /**
     * index.
     *
     * @return array
     */
    public function index($page_number, $_format)
    {
        // put action your code here
        $page_from = self::$page_size * ($page_number - 1);
        $page_to = self::$page_size * $page_number;
        $end = false;

        $repository = $this->getDoctrine()
            ->getRepository('Odalisk\Entity\Portal');
        $portals = $repository->findAll();

        $end = (count($portals) < self::$page_size) ? true : false;

        return $this->render('App:Portal:index.html.twig', array(
            'maintenance_status' => $this->container->getParameter('app.maintenance'),
            'page_number' => $page_number,
            'portals' => $portals,
            'page_number' => $page_number,
            'page_from' => $page_from,
            'page_to' => $page_to,
            'end' => $end));
    }

    /**
     * details.
     *
     * @return array
     */
    public function details($portal_number)
    {
        // put action your code here
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('Odalisk\Entity\Portal');
        $portal = $repository->findOneById($portal_number);
        
        return $this->render('App:Portal:details.html.twig', array(
            'maintenance_status' => $this->container->getParameter('app.maintenance'),
            'portal' => $portal));
    }
    
    public function getPortalList()
    {
        
    }
}
