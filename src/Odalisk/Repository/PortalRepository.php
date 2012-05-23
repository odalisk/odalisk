<?php
namespace Odalisk\Repository;

use Doctrine\ORM\EntityRepository;


class PortalRepository extends EntityRepository
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

	  public function getFormatDistribution($portal){

	  	$stmt = $this->getEntityManager()
	  			->getConnection()
	  			->prepare('SELECT format, COUNT(*) FROM ( SELECT id FROM `datasets` WHERE portal_id = ? ) as d JOIN  `dataset_format` ON d.id = dataset_id, formats WHERE  `formats`.id =  `format_id` GROUP BY format'
	  			);

	  	$stmt->bindValue(1, $portal->getId());
	  	$stmt->execute();
	  	$res = $stmt->fetchAll();

	  	$output = array();
	  	foreach ($res as $key => $value) {
	  		$output[$value['format']] = $value['COUNT(*)'];
	  	}

	  	return $output;
	  }

	  public function getCategoryDistribution($portal){

	  	$stmt = $this->getEntityManager()
	  			->getConnection()
	  			->prepare('SELECT category, COUNT(*) FROM ( SELECT id FROM `datasets` WHERE portal_id = :portal_id ) as d JOIN dataset_category on `dataset_id` = d.id, categories WHERE `category_id` = categories.id
					group by category'
	  			);

	  	$stmt->bindValue("portal_id", $portal->getId());
	  	$stmt->execute();
	  	$res = $stmt->fetchAll();

	  	$output = array();
	  	foreach ($res as $key => $value) {
	  		$output[$value['category']] = $value['COUNT(*)'];
	  	}

	  	return $output;
	  }


	  public function findAllWithLimit()
      {
          return $this->getEntityManager()
              ->createQuery('SELECT p FROM Odalisk\Entity\Portal p ORDER BY p.name ASC')
              ->limit(5)
              ->getResult();
      }
	  
	  
}
