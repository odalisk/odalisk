<?php
namespace Odalisk\Repository;

use Doctrine\ORM\EntityRepository;

class DatasetRepository extends EntityRepository
{
    public function getFileFormatsCount($datasetId)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                    SELECT count(*)
                    FROM formats JOIN dataset_format ON (id = format_id)
                    WHERE dataset_id = :datasetId
                    GROUP BY(format);
            ')->setParameter('datasetId', $datasetId)
        ;


    }

}
