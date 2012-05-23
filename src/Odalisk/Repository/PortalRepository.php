<?php
namespace Odalisk\Repository;

use Doctrine\ORM\EntityRepository;

class PortalRepository extends EntityRepository
{
    public function getPortalCountries()
    {
         $data = $this->getEntityManager()
            ->createQuery('
                SELECT DISTINCT p.country 
                FROM Odalisk\Entity\Portal p
                ORDER BY p.country ASC')
            ->getResult();
        
        $result = array();
        foreach($data as $row) {
            $result[] = $row['country'];
        }
        return $result;
    }
    
    public function getPortalEntities()
    {
         $data = $this->getEntityManager()
            ->createQuery('
                SELECT DISTINCT p.entity 
                FROM Odalisk\Entity\Portal p
                ORDER BY p.entity ASC')
            ->getResult();
        
        $result = array();
        foreach($data as $row) {
            $result[] = $row['entity'];
        }
        return $result;
    }
    
    public function getPortalStatuses()
    {
         $data = $this->getEntityManager()
            ->createQuery('
                SELECT DISTINCT p.status 
                FROM Odalisk\Entity\Portal p
                ORDER BY p.status ASC')
            ->getResult();

        $result = array();
        foreach($data as $row) {
            $result[] = $row['status'];
        }
        return $result;
    }
    
    public function getDatasetsCount($portal)
      {
        return $this->getEntityManager()
                ->createQuery('SELECT count(d) FROM Odalisk\Entity\Dataset d WHERE d.portal = :portal')
                ->setParameter('portal', $portal)
                ->getSingleScalarResult();

      }


      public function getInChargePersonCount($portal)
      {
        return $this->getEntityManager()
                ->createQuery('SELECT count(d) FROM Odalisk\Entity\Dataset d WHERE d.portal = :portal and (d.owner is not null or d.maintainer is not null or d.provider is not null)')
                ->setParameter('portal', $portal)
                ->getSingleScalarResult();

      }

      public function getReleasedOnExistCount($portal)
      {
        return $this->getEntityManager()
                ->createQuery('SELECT count(d) FROM Odalisk\Entity\Dataset d WHERE d.portal = :portal and d.released_on is not null')
                ->setParameter('portal', $portal)
                ->getSingleScalarResult();

      }

      public function getLastUpdatedOnExistCount($portal)
      {

        return $this->getEntityManager()
                ->createQuery('SELECT count(d) FROM Odalisk\Entity\Dataset d WHERE d.portal = :portal and d.last_updated_on is not null')
                ->setParameter('portal', $portal)
                ->getSingleScalarResult();
      }

      public function getCategoryExistCount($portal)
      {

          $stmt = $this->getEntityManager()
             ->getConnection()
             ->prepare("
                        SELECT count(*) FROM datasets WHERE portal_id = ".$portal->getId()." and id IN (SELECT DISTINCT dataset_id FROM dataset_category)"
        );

        return $stmt->execute();

      }

      public function getSummaryAndTitleAtLeastCount($portal)
      {
        return $this->getEntityManager()
                ->createQuery('SELECT count(d) FROM Odalisk\Entity\Dataset d WHERE d.portal = :portal and d.name is not null and d.summary is not null')
                ->setParameter('portal', $portal)
                ->getSingleScalarResult();

      }

      public function getLicenseCount($portal)
      {
        return $this->getEntityManager()
                ->createQuery('SELECT count(d) FROM Odalisk\Entity\Dataset d WHERE d.portal = :portal and d.license is not null')
                ->setParameter('portal', $portal)
                ->getSingleScalarResult();

      }

      public function findAllWithLimit()
      {
          return $this->getEntityManager()
              ->createQuery('SELECT p FROM Odalisk\Entity\Portal p ORDER BY p.name ASC')
              ->limit(5)
              ->getResult();
      }


}
