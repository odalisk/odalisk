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
    public function details($portal_number, $page_number, $_format)
    {
        $request = Request::createFromGlobals();
        $result = '';



        // put action your code here
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('Odalisk\Entity\Portal');
        $portal = $repository->findOneById($portal_number);

        $page_from = self::$page_size * ($page_number - 1);

        $session = $this->getRequest()->getSession();

        $searchsession = $session->get('search');
        $searchpost = $request->request->get('search');

        $search = ($request->getMethod() == 'POST') ? $searchpost : false;

        $search = (!$search  && $searchsession != '') ? $searchsession : $search;

        if ($search)
        {
            $session->set('search', $search);
            $datasets = $repository->findByPortalSearch($portal->getId(), $page_from, self::$page_size, $search);
            $page_from = 0;
            $page_to = self::$page_size;
            error_log(json_encode($datasets));
        }
        else
        {
            $datasets = $portal->getDatasets()->slice($page_from,self::$page_size);
        }


        $page_to = self::$page_size * ($page_number - 1) + count($datasets);

        $end = (count($datasets) < self::$page_size) ? true : false;


        return $this->render('App:Portal:details.html.twig', array(
            'maintenance_status' => $this->container->getParameter('app.maintenance'),
            'portal' => $portal,
            'datasets' => $datasets,
            'page_number' => $page_number,
            'page_from' => $page_from,
            'page_to' => $page_to,
            'end' => $end,
            'search' => $search));
    }
    
    public function getPortalList()
    {
        
    }
}
