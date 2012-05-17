<?php
namespace Odalisk\Repository;

use Doctrine\ORM\EntityRepository;

class StatisticRepository extends EntityRepository
{
      
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
            ->createQuery('SELECT count(d) FROM Odalisk\Entity\Dataset d WHERE d.portal = :portal and (d.owner is not null or d.maintainer is not null)')
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
    return $this->getEntityManager()
            ->createQuery('SELECT count(d) FROM Odalisk\Entity\Dataset d WHERE d.portal = :portal and d.category is not null')
            ->setParameter('portal', $portal)
            ->getSingleScalarResult();

  }

  public function getSummaryAndTitleAtLeastCount($portal)
  {
    return $this->getEntityManager()
            ->createQuery('SELECT count(d) FROM Odalisk\Entity\Dataset d WHERE d.portal = :portal and d.name is not null and d.summary is not null')
            ->setParameter('portal', $portal)
            ->getSingleScalarResult();

  }
  
  public function findByPortalSearch($portal_id, $page_from, $page_size,$search)
  {
      return $this->getEntityManager()
          ->createQuery('SELECT d 
                         FROM Odalisk\Entity\Portal as p, Odalisk\Entity\Dataset as d
                         WHERE 
                              p.id = :portal_id 
                              AND d.portal = p.id
                              AND d.name LIKE :search 
                         ORDER BY d.name ASC')
          ->setFirstResult($page_from)
          ->setMaxResults($page_size)
          ->setParameter('portal_id', $portal_id)
          ->setParameter('search', '%'.$search.'%')
          ->getResult();
  }

}
