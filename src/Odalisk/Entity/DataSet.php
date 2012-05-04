<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class DataSet
{   
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    protected $url;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;
    
    /**
     * @var string $summary
     *
     * @ORM\Column(name="summary", type="text")
     */
    protected $summary;
    
    /**
     * @var string $category
     *
     * @ORM\Column(name="category", type="string", length=255)
     */
    protected $category;
    
    /**
     * @var string $released_on When did we create this record
     *
     * @ORM\Column(name="released_on", type="datetime")
     */
    protected $released_on;
    
    /**
     * @var string $last_updated_on When did we create this record
     *
     * @ORM\Column(name="last_updated_on", type="datetime")
     */
    protected $last_updated_on;
    
    /**
     * @var string $owner
     *
     * @ORM\Column(name="owner", type="string", length=255)
     */
    protected $owner;
    
    /**
     * @var string $maintainer
     *
     * @ORM\Column(name="maintainer", type="string", length=255)
     */
    protected $maintainer;
    
    /**
     * @var string $license
     * 
     * @ORM\Column(name="license", type="string", length=255)
     */
    protected $license;
    
    /**
     * @var string $created_at When did we create this record
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $created_at;
    
    /**
     * @var string $updated_at When did we update this record
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updated_at;
}
