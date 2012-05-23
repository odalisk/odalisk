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

}
