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
        $result = $this->constructQuery($params);
        if($_format == 'json')
        {
            $response = new Response(json_encode($result)); 
        }
        else
        {
            $response = $this->render('App:Api:dataset.html.twig', array('datasets' => $result));
        }
        return $response;
    }
    
    private function constructQuery($request)
    {
        $repository = $this->getDoctrine()
            ->getRepository('Odalisk\Entity\Dataset');

        $qb = $repository->createQueryBuilder('d');
        
        $result = $this->getWhere($request);
        $qb->addSelect('d')
           ->addSelect('p.id as portal_id, p.name as portal_name')
           ->add('from', 'Odalisk\Entity\Portal p, Odalisk\Entity\Dataset d')
           ->add('where', $result['where'])
           ->add('orderBy', 'd.name ASC')
           ->setFirstResult(0)
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
        
        $where .= 'p.id = d.portal';
        error_log($where);
        return array('where' => $where,
                     'params' => $params);
    }
}