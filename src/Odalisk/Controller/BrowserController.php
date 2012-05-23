<?php

namespace Odalisk\Controller;

use Knp\Bundle\RadBundle\Controller\Controller;

/**
 * Portal controller.
 */
class BrowserController extends Controller
{

    /**
     * index.
     *
     * @return array
     */
    public function index($_format)
    {


        $repository = $this->getDoctrine()
            ->getRepository('Odalisk\Entity\Portal');
        $portals = $repository->findAll();


        return $this->render('App:Browser:index.html.twig', array(
            'maintenance_status' => $this->container->getParameter('app.maintenance'),
            'portals' => $portals));
    }
}
