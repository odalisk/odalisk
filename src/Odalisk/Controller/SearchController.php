<?php

namespace Odalisk\Controller;

use Knp\Bundle\RadBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
/**
 * Search controller.
 */
class SearchController extends Controller
{

    /**
     * search.
     *
     * @return array
     */
    public function search()
    {
        return $this->render('App:Search:search.html.twig', array(
            'maintenance_status' => $this->container->getParameter('app.maintenance')));
    }
}