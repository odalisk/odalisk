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
    public function portals(Request $request, $page_index, $page_size, $display)
    {
        $data = $request->request->all();
        var_dump($data);
        $em = $this->getEntityRepository('Odalisk\Entity\Portal');
        if(isset($data['request'])) {
            $portals = $em->getPortalsMatching($data['request'], $page_index, $page_size);
        } else {
            $portals = $em->findBy(array(), array('id' => 'ASC'), $page_size, $page_index * $page_size);
        }
        
        return $this->render('App:Api:portal.html.twig', array(
            'portals' => $portals,
            'display' => $display
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
    public function datasets(Request $request, $page_index, $page_size, $display) {
        $data = $request->request->all();
        $em = $this->getEntityRepository('Odalisk\Entity\Dataset');
        if(isset($data['request'])) {
            $datasets = $em->getDatasetsMatching($data['request'], $page_index, $page_size);
        } else {
            $datasets = $em->findBy(array(), array('id' => 'ASC'), $page_size, $page_index * $page_size);
        }
         
        return $this->render('App:Api:dataset.html.twig', array(
            'datasets' => $datasets,
            'pagenumber' => 0,
        ));
    }

    public function datasetTags($current_portal)
    {
        $er = $this->getEntityRepository('Odalisk\Entity\Portal');
        $categories = array();
        $portals = array();
        $formats = array();
        $licenses = array();
        if (null != $current_portal) {
            $categories = $er->getCategories($current_portal);
            $formats = $er->getFormats($current_portal);
            $licenses = $er->getLicenses($current_portal);
        } else {
            $portals = $er->findAll();
            $categories = $this->getEntityRepository('Odalisk\Entity\Category')
                               ->findall();
            $formats = $this->getEntityRepository('Odalisk\Entity\Format')
                            ->findall();
            $licenses = $this->getEntityRepository('Odalisk\Entity\License')
                             ->findall();
        }
        
        
        return $this->render('App:Api:dataset-tags.html.twig', array(
            'portals' => $portals,
            'current_portal' => $current_portal,
            'categories' => $categories,
            'formats' => $formats,
            'licenses' => $licenses,
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
