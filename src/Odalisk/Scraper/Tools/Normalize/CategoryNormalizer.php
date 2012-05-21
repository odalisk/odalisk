<?php

namespace Odalisk\Scraper\Tools\Normalize;

use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

class CategoryNormalizer
{
    private $replace = array(
        '/\b(and|et)\b/' => '&',
        '/^(Not (A|a)pplicable|Not Av|none|N|A|[0-9\.]+||\s+|)$/' => 'N/A',
    );
    
    protected $categories = array();
    
    protected $aliases = array();
        
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getEntityManager();
    }
    
    public function init($yaml) {
        foreach($yaml as $category => $data) {
            $c = new \Odalisk\Entity\Category($category);
            foreach($data['aliases'] as $alias) {
                $c->addAlias($alias);
                $this->aliases[$alias] = $category;
            }
            $this->em->persist($c);
            $this->em->flush();
            $this->categories[$category] = $c;
        }
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
            if(array_key_exists($category, $this->categories)) {
                //error_log('Base category');
                $result[] = $this->categories[$category];
            } elseif (array_key_exists($category, $this->aliases)) {
                //error_log('Alias category');
                $result[] = $this->categories[$this->aliases[$category]];
            } else {
                //error_log('Other');
                $result[] = $this->categories['Other'];
            }
        }
        $result['raw'] = implode(', ', $categories);
        return array_unique($result);
    }
    
    private function _trim($value)
    {
        return trim($value, " \t\n\r\0\x0B\"'[]()&.");
    }
}