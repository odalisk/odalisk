<?php

namespace Odalisk\Controller;

use Knp\Bundle\RadBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
/**
 * Api controller.
 */
class ApiController extends Controller
{
    private $type;
    
    
    private $page_size = 20;
    /**
     * api.
     */
    public function api($_format)
    {
        
        $request = $this->getRequest();
        $params = $request->request->all();
        $this->type = $params['type'];
        
        $params['page_number'] = intval($params['page_number']);
        $this->page_size = (isset($params['page_size'])) ? intval($params['page_size']) : $this->page_size;
        
        
        $result = $this->constructQuery($params);
        
        
        if($_format == 'json')
        {
            $response = new Response(json_encode($result)); 
        }
        else
        {
            if($this->type == 'dataset')
            {
                $response = $this->render('App:Api:dataset.html.twig', array('datasets' => $result,
                                                                             'pagenumber' => $params['page_number']));
            }
            else if($this->type == 'portal')
            {
                $response = $this->render('App:Api:portal.html.twig', array('portals' => $result,
                                                                             'pagenumber' => $params['page_number']));
            }
        }
        
        return $response;
    }
    
    public function datasetTags($current_portal)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('Odalisk\Entity\Portal');
        $portals = $repository->findAll();
        return $this->render('App:Api:dataset-tags.html.twig', array('portals' => $portals,
                                                                  'current_portal' => $current_portal));
    }
    
    public function portalTags()
    {
        return $this->render('App:Api:portal-tags.html.twig', array());
    }
    
    private function constructQuery($request)
    {
        $repository = $this->getDoctrine()
            ->getRepository('Odalisk\Entity\Dataset');
        
        
        $forWhere = (isset($request['request'])) ? $request['request'] : array() ;
        $result = $this->getWhere($forWhere);
        
        $qb = null;
        if($this->type == 'dataset')
        {
            $qb = $repository->createQueryBuilder('d');
            $qb->addSelect('d')
               ->addSelect('p.id as portal_id, p.name as portal_name')
               ->add('from', 'Odalisk\Entity\Portal p, Odalisk\Entity\Dataset d')
               ->add('orderBy', 'd.name ASC');
        }
        else if($this->type == 'portal')
        {
            $qb = $repository->createQueryBuilder('p');
            $qb->addSelect('p')
               ->add('from', 'Odalisk\Entity\Portal p')
               ->add('orderBy', 'p.name ASC');
        }
        
        $qb->add('where', $result['where'])
            ->setFirstResult($this->page_size * $request['page_number'])
            ->setMaxResults($this->page_size)
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
        
        
        if($this->type == 'dataset')
        {
            if(isset($request['search']))
            {
                $where .= 'd.name like :name';
                $params['name'] = '%'.$request['search'].'%';
                $first = false;
            }
            
            $where .= (!$first) ? ' AND ' : ' ';
            $first = true;
        
            $where .= 'p.id = d.portal AND (d.name IS NOT NULL OR d.name != \'\')';
        }
        else if($this->type == 'portal')
        {
            if(isset($request['search']))
            {
                $where .= 'p.name like :name';
                $params['name'] = '%'.$request['search'].'%';
                $first = false;
            }
        }
        
        error_log($where);
        error_log(json_encode($params));
        return array('where' => $where,
                     'params' => $params);
    }
    
    
    
}