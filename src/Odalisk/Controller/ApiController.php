<?php

namespace Odalisk\Controller;

use Knp\Bundle\RadBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
/**
 * Api controller.
 */
class ApiController extends Controller
{
    /**
     * api.
     */
    public function api($_format)
    {
        
        $request = $this->getRequest();
        $params = $request->request->all();
        $params['page_number'] = intval($params['page_number']);
        $result = $this->constructQuery($params);
        
        if($_format == 'json')
        {
            $response = new Response(json_encode($result)); 
        }
        else
        {
            if(count($result) != 0)
            {
                $response = $this->render('App:Api:dataset.html.twig', array('datasets' => $result,
                                                                             'pagenumber' => $params['page_number']));
            }
            else
            {
                $response = $this->render('App:Api:dataset.html.twig', array('datasets' => array(),
                                                                             'pagenumber' => $params['page_number']));
            }
        }
        
        return $response;
    }
    
    public function tagsList($current_portal)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('Odalisk\Entity\Portal');
        $portals = $repository->findAll();
        return $this->render('App:Api:tags-list.html.twig', array('portals' => $portals,
                                                                  'current_portal' => $current_portal));
    }
    
    private function constructQuery($request)
    {
        $repository = $this->getDoctrine()
            ->getRepository('Odalisk\Entity\Dataset');

        $qb = $repository->createQueryBuilder('d');
        
        $forWhere = (isset($request['request'])) ? $request['request'] : array() ;
        
        $result = $this->getWhere($forWhere);
        
        $qb->addSelect('d')
           ->addSelect('p.id as portal_id, p.name as portal_name')
           ->add('from', 'Odalisk\Entity\Portal p, Odalisk\Entity\Dataset d')
           ->add('where', $result['where'])
           ->add('orderBy', 'd.name ASC')
           ->setFirstResult(20 * $request['page_number'])
           ->setMaxResults(20)
           ->setParameters($result['params']);
        
        $query = $qb->getQuery();
        
        return $query->getArrayResult();
        
    }
    
    private function getWhere($request)
    {
        $params = array();
        $where = '';
        $first = true;
        
        if(isset($request['portal_id']))
        {
            $portals = $request['portal_id'];
            $i = 0;
            foreach($portals as $key => $value)
            {
                $i++;
                $where .= (!$first) ? ' OR ' : ' ';
                $first = false;
                $where .= 'p.id = :portal_id_'.$key;
                $params['portal_id_'.$key] = $value;
            }
        }
        
        $where .= (!$first) ? ' AND ' : ' ';
        $first = true;
        
        if(isset($request['category']))
        {
            $portals = $request['category'];
            $i = 0;
            foreach($portals as $key => $value)
            {
                $i++;
                $where .= (!$first) ? ' OR ' : ' ';
                $first = false;
                $where .= 'd.category like :category_'.$key;
                $params['category_'.$key] = '%'.$value.'%';
            }
        }
        
        $where .= (!$first) ? ' AND ' : ' ';
        $first = true;
        
        if(isset($request['portal']))
        {
            $portals = $request['portal'];
            $i = 0;
            foreach($portals as $key => $value)
            {
                $i++;
                $where .= (!$first) ? ' OR ' : ' ';
                $first = false;
                $where .= 'd.portal = :portal_'.$key;
                $params['portal_'.$key] = $value;
            }
        }
        
        $where .= (!$first) ? ' AND ' : ' ';
        $first = true;
        
        if(isset($request['search']))
        {
            error_log($request['search']);
            $where .= 'd.name like :name';
            $params['name'] = '%'.$request['search'].'%';
            $first = false;
        }
        
        $where .= (!$first) ? ' AND ' : ' ';
        $first = true;
        
        $where .= 'p.id = d.portal AND (d.name IS NOT NULL OR d.name != \'\')';
        error_log($where);
        error_log(json_encode($params));
        return array('where' => $where,
                     'params' => $params);
    }
    
    
    
}