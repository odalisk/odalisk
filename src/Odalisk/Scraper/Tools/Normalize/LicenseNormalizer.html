<?php

namespace Odalisk\Scraper\Tools\Normalize;

use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

class LicenceNormalizer
{
    private $replace = array(
        '/\b(and|et)\b/' => '&',
        '/^(Not (A|a)pplicable|Not Av|none|N|A|[0-9\.]+||\s+|)$/' => 'N/A',
    );
    
    protected $licenses = array();
    
    protected $aliases = array();
        
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getEntityManager();
    }
    
    public function init($yaml) {
        foreach($yaml as $category => $data) {
            $c = new \Odalisk\Entity\License($category);
            
            /*foreach($data['aliases'] as $alias) {
                $c->addAlias($alias);
                $this->aliases[strtolower($alias)] = strtolower($category);
            }
            $this->em->persist($c);
            $this->em->flush();
            $this->categories[strtolower($category)] = $c;
            
        }
        // var_dump($this->categories);
        // var_dump($this->aliases);
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
            $category = strtolower($category);
            if(array_key_exists($category, $this->categories)) {
                $result[$category] = $this->categories[$category];
            } elseif (array_key_exists($category, $this->aliases)) {
                $result[$this->aliases[$category]] = $this->categories[$this->aliases[$category]];
            } else {
                $result['other'] = $this->categories['other'];
            }
        }
        $result['raw'] = implode(', ', $categories);
        
        return $result;
    }
    
    private function _trim($value)
    {
        return trim($value, " \t\n\r\0\x0B\"'[]()&.");
    }
}
