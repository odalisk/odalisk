<?php
namespace Odalisk\Repository;

use Doctrine\ORM\EntityRepository;

class DatasetRepository extends EntityRepository
{
    public function getDatasetsMatching($criterias, $page_index, $page_size) {
        error_log(print_r($criterias, true));
        $qb = $this->createQueryBuilder('d');
        if(array_key_exists('in', $criterias)) {
            // JOIN ... WITH IN (...)
            $join = 0;
            foreach($criterias['in'] as $column => $values) {
                $qb->join('d.' . $column, 'j' . $join, 'WITH', $qb->expr()->in('j' . $join . '.id', $values));
                $join++;
            }
        }
        
        if(array_key_exists('where', $criterias)) {
            // WHERE clause
            $cond = 0;
            $parameters = array();
            foreach($criterias['where'] as $condition) {
                if(0 == $cond) {
                    $qb->where('d.' . $condition[0] . ' ' . $condition[1] . ' :p' . $cond);
                } else {
                    $qb->andWhere('d.' . $condition[0] . ' ' . $condition[1] . ' :p' . $cond);
                }

                $parameters['p' . $cond] = $condition[2];
                $cond++;
            }
            $qb->setParameters($parameters);
        }
        
        $qb->orderBy('d.name', 'ASC');
        $qb->setFirstResult($page_index * $page_size);
        $qb->setMaxResults($page_size);
        
        error_log($qb->getDql());
        
        return $qb->getQuery()->getResult();
    }
}
