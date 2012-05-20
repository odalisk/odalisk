<?php

namespace Odalisk\Scraper\Tools\Normalize;

class CategoryNormalizer
{
    private $replace = array(
        '/\b(and|et)\b/' => '&',
        '/^(Not (A|a)pplicable|Not Av|none|N|A|[0-9\.]+||\s+|)$/' => 'N/A',
    );
    
    private $categoryList = array();
    
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getEntityManager();
    }
    
    public function getCategories($raw_categories)
    {
        // Extract clean categories
        $categories = preg_split('/(,|;|\/|\|_)/', $raw_categories);
        foreach ($categories as $k => $category) {
            $categories[$k] = $this->_trim($categories[$k]);
            foreach ($this->replace as $bad => $good) {
                $categories[$k] = preg_replace($bad, $good, $categories[$k]);
            }
            $categories[$k] = $this->_trim($categories[$k]);
        }
        
        $categories = array_unique(array_filter($categories));
        
        $result = array();
        foreach($categories as $category) {
            if(!array_key_exists($category, $this->categoryList)) {
                $this->categoryList[$category] = new \Odalisk\Entity\Category($category);
            }
            $result[] = $this->categoryList[$category];
        }
        
        return $result;
    }
    
    private function _trim($value)
    {
        return trim($value, " \t\n\r\0\x0B\"'[]()&.");
    }
}