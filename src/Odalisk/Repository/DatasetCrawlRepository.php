<?php

namespace Odalisk\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * DatasetCrawlRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DatasetCrawlRepository extends EntityRepository
{
    public function getSuccessfullCrawls($portal) {
        $query = $this->getEntityManager()
            ->createQuery('
                    SELECT MAX(c.id), c
                    FROM Odalisk\Entity\DatasetCrawl c
                    WHERE c.code = 200 and c.portal = :portal
                    GROUP BY c.hash
            ')->setParameter('portal', $portal)
        ;
        
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return NULL;
        }
    }
    
    public function getErrorRate() {
        $stmt = $this->getEntityManager()
                     ->getConnection()
                     ->prepare('
                        SELECT count(*) / (SELECT count(*) FROM datasets_crawl) as errors
                        FROM datasets_crawl
                        WHERE code <> 200 OR code IS NULL'
        );
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}