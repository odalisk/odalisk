<?php

namespace Odalisk\Controller;

use Knp\Bundle\RadBundle\Controller\Controller;

/**
 * Dataset controller.
 */
class DatasetController extends Controller
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

    /**
     * details.
     *
     * @return array
     */
    public function details($dataset_number, $_format)
    {
        // put action your code here

        $repository = $this->getDoctrine()
            ->getRepository('Odalisk\Entity\Dataset');
        $dataset = $repository->findById($dataset_number);
        $dataset = $dataset[0];

        return $this->render('App:Dataset:details.html.twig', array(
            'maintenance_status' => $this->container->getParameter('app.maintenance'),
            'dataset' => $dataset));
    }
}
