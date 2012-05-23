<?php

namespace Odalisk\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Odalisk\Entity\PortalCriteria
 *
 * @ORM\Table(name="portal_criteria")
 * @ORM\Entity
 */
class PortalCriteria
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
	 * @var string $search_engine
	 *
	 * @ORM\Column(name="search_engine", type="boolean", nullable=false)
	 */
	protected $search_engine;

	/**
	 * @var string $metadata_search
	 *
	 * @ORM\Column(name="metadata_search", type="boolean", nullable=false)
	 */
	protected $metadata_search;

	/**
	 * @var string $filtering_license_search
	 *
	 * @ORM\Column(name="filtering_license_search", type="boolean", nullable=false)
	 */
	protected $filtering_license_search;

	/**
	 * @var string $forum_section
	 *
	 * @ORM\Column(name="forum_section", type="boolean", nullable=false)
	 */
	protected $forum_section;

	/**
	 * @var string $contact_form
	 *
	 * @ORM\Column(name="contact_form", type="boolean", nullable=false)
	 */
	protected $contact_form;

	/**
	 * @var string $rating_dataset
	 *
	 * @ORM\Column(name="rating_dataset", type="boolean", nullable=false)
	 */
	protected $rating_dataset;

	/**
	 * @var string $comment_dataset
	 *
	 * @ORM\Column(name="comment_dataset", type="boolean", nullable=false)
	 */
	protected $comment_dataset;

	/**
	 * @var string $suggest_dataset
	 *
	 * @ORM\Column(name="suggest_dataset", type="boolean", nullable=false)
	 */
	protected $suggest_dataset;

	/**
	 * @var string $api
	 *
	 * @ORM\Column(name="api", type="boolean", nullable=false)
	 */
	protected $api;

	/**
	 * @var string $api_documentation
	 *
	 * @ORM\Column(name="api_documentation", type="boolean", nullable=false)
	 */
	protected $api_documentation;

	/**
	 * @var string $opendata_concept_explained
	 *
	 * @ORM\Column(name="opendata_concept_explained", type="boolean", nullable=false)
	 */
	protected $opendata_concept_explained;


    /**
     * Set search_engine
     *
     * @param boolean $searchEngine
     */
    public function setSearchEngine($searchEngine)
    {
        $this->search_engine = $searchEngine;
    }

    /**
     * Get search_engine
     *
     * @return boolean 
     */
    public function getSearchEngine()
    {
        return $this->search_engine;
    }

    /**
     * Set metadata_search
     *
     * @param boolean $metadataSearch
     */
    public function setMetadataSearch($metadataSearch)
    {
        $this->metadata_search = $metadataSearch;
    }

    /**
     * Get metadata_search
     *
     * @return boolean 
     */
    public function getMetadataSearch()
    {
        return $this->metadata_search;
    }

    /**
     * Set filtering_license_search
     *
     * @param boolean $filteringLicenseSearch
     */
    public function setFilteringLicenseSearch($filteringLicenseSearch)
    {
        $this->filtering_license_search = $filteringLicenseSearch;
    }

    /**
     * Get filtering_license_search
     *
     * @return boolean 
     */
    public function getFilteringLicenseSearch()
    {
        return $this->filtering_license_search;
    }

    /**
     * Set forum_section
     *
     * @param boolean $forumSection
     */
    public function setForumSection($forumSection)
    {
        $this->forum_section = $forumSection;
    }

    /**
     * Get forum_section
     *
     * @return boolean 
     */
    public function getForumSection()
    {
        return $this->forum_section;
    }

    /**
     * Set contact_form
     *
     * @param boolean $contactForm
     */
    public function setContactForm($contactForm)
    {
        $this->contact_form = $contactForm;
    }

    /**
     * Get contact_form
     *
     * @return boolean 
     */
    public function getContactForm()
    {
        return $this->contact_form;
    }

    /**
     * Set rating_dataset
     *
     * @param boolean $ratingDataset
     */
    public function setRatingDataset($ratingDataset)
    {
        $this->rating_dataset = $ratingDataset;
    }

    /**
     * Get rating_dataset
     *
     * @return boolean 
     */
    public function getRatingDataset()
    {
        return $this->rating_dataset;
    }

    /**
     * Set comment_dataset
     *
     * @param boolean $commentDataset
     */
    public function setCommentDataset($commentDataset)
    {
        $this->comment_dataset = $commentDataset;
    }

    /**
     * Get comment_dataset
     *
     * @return boolean 
     */
    public function getCommentDataset()
    {
        return $this->comment_dataset;
    }

    /**
     * Set suggest_dataset
     *
     * @param boolean $suggestDataset
     */
    public function setSuggestDataset($suggestDataset)
    {
        $this->suggest_dataset = $suggestDataset;
    }

    /**
     * Get suggest_dataset
     *
     * @return boolean 
     */
    public function getSuggestDataset()
    {
        return $this->suggest_dataset;
    }

    /**
     * Set api
     *
     * @param boolean $api
     */
    public function setApi($api)
    {
        $this->api = $api;
    }

    /**
     * Get api
     *
     * @return boolean 
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Set api_documentation
     *
     * @param boolean $apiDocumentation
     */
    public function setApiDocumentation($apiDocumentation)
    {
        $this->api_documentation = $apiDocumentation;
    }

    /**
     * Get api_documentation
     *
     * @return boolean 
     */
    public function getApiDocumentation()
    {
        return $this->api_documentation;
    }

    /**
     * Set opendata_concept_explained
     *
     * @param boolean $opendataConceptExplained
     */
    public function setOpendataConceptExplained($opendataConceptExplained)
    {
        $this->opendata_concept_explained = $opendataConceptExplained;
    }

    /**
     * Get opendata_concept_explained
     *
     * @return boolean 
     */
    public function getOpendataConceptExplained()
    {
        return $this->opendata_concept_explained;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}