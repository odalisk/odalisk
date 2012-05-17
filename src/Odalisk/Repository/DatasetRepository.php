<?php
namespace Odalisk\Repository;

use Doctrine\ORM\EntityRepository;

class DatasetRepository extends EntityRepository
{
    public function findByIdAndPage($id,$page_number)
    {
        /*return $this->getEntityManager()
            ->createQuery('SELECT * FROM 
                (
                    SELECT * FROM App:Portal as p
                    WHERE id = :id
                ) as portal
                JOIN
                (
                    SELECT * FROM App:Dataset as d
                    WHERE portal_id = :id
                    ORDER BY d.name ASC
                    LIMIT 20
                ) as dataset ON portal.id = dataset.portal_id
                ORDER BY portal.name ASC
                LIMIT 20')
            ->getResult();*/
    }
}