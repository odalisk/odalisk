<?php

namespace Odalisk\Controller;

use Knp\Bundle\RadBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
/**
 * Api controller.
 */
class ApiController extends Controller
{  
    public function portals($page_index, $page_size)
    {
        $portals = $this->getEntityRepository('Odalisk\Entity\Portal')
            ->findBy(array(), array('name' => 'ASC'), $page_size, $page_index * $page_size);
        
        return $this->render('App:Api:portal.html.twig', array(
            'portals' => $portals,
        ));
    }
    
    /**
     * $params = array(
     *    'in' => array(
     *        'portal' => array(1,2),
     *        'categories' => array(1,2),
     *    ),
     *    // WHERE name LIKE %test% AND id > 4
     *    'where' => array(
     *        array('name', 'LIKE', '%test%'),
     *        array('id', '>', 4),
     *    ),
     * );
     *
     * @param Request $request 
     * @return void
     */
    public function datasets(Request $request) {
        $datasets = $this->getEntityRepository('Odalisk\Entity\Dataset')
            ->getDatasetsMatching($params);
        
        //var_dump($datasets);
            
        $this->render('App:Api:dataset.html.twig', array(
            'datasets' => $datasets,
            'pagenumber' => 0,
        ));
    }

    public function datasetTags($current_portal)
    {
        $portals = $this->getEntityRepository('Odalisk\Entity\Portal')->findAll();

        return $this->render('App:Api:dataset-tags.html.twig', array(
            'portals' => $portals,
            'current_portal' => $current_portal
        ));
    }

    public function portalTags()
    {
        $er = $this->getEntityRepository('Odalisk\Entity\Portal');
        $countries = $er->getPortalCountries();
        $statuses = $er->getPortalStatuses();
                
        return $this->render('App:Api:portal-tags.html.twig', array(
            'countries' => $countries,
            'statuses' => $statuses,
        ));
    }
}
