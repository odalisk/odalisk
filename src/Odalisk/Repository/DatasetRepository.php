<?php
namespace Odalisk\Repository;

use Doctrine\ORM\EntityRepository;

class DatasetRepository extends EntityRepository
{
    public function getFormats($datasetId)
    {
        $sth = $this->getEntityManager()
            ->getConnection()
            ->prepare('
                    SELECT (formats.format)
                    FROM formats JOIN dataset_format ON (id = format_id)
                    WHERE dataset_id = :datasetId
                    GROUP BY(format);
                    ');
        $sth = $sth->execute(array('datasetId' => $datasetId));
        $res = $sth->fetchColumn();
    }

    public function getDatasetsMatching($criterias) {
        $qb = $this->createQueryBuilder('d');
        // JOIN ... WITH IN (...)
        $join = 0;
        foreach($criterias['in'] as $column => $values) {
            $qb->leftJoin('d.' . $column, 'j' . $join, 'WITH', $qb->expr()->in('j' . $join . '.id', $values));
            $join++;
        }
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
        
        $qb->orderBy('d.name', 'ASC');
        error_log($qb->getDql());
        
        return $qb->getQuery()->getResult();
    }
}
