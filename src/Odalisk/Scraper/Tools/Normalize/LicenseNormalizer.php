<?php

namespace Odalisk\Scraper\Tools\Normalize;


class LicenseNormalizer
{
    private $replace = array(
        '/\b(and|et)\b/' => '&',
        '/^(Not (A|a)pplicable|Not Av|none|N|A|[0-9\.]+||\s+|)$/' => 'N/A',
    );

    protected $licenses = array();

    protected $aliases = array();
    
    protected $regex = array();

    public function __construct($doctrine, $log)
    {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getEntityManager();
        $this->er = $this->em->getRepository('Odalisk\Entity\License');

        $this->log = $log;
    }

    public function init($yaml)
    {
        foreach ($yaml as $name => $license) {
            $l = $this->er->findOneByName($name);
            if (!$l) {
                $l = new \Odalisk\Entity\License($name);
            }
            foreach ($license['aliases'] as $alias) {
                $l->addAlias($alias);
                $this->aliases[strtolower($alias)] = strtolower($name);
            }
            if(isset($license['regex'])) {
                $this->regex[$license['regex']] = strtolower($name);
            }     
            
            $l->setAuthorship($license['authorship']);
            $l->setReuse($license['reuse']);
            $l->setRedistribution($license['redistribution']);
            $l->setCommercial($license['commercial']);
            $l->setIsGood();
            $l->setQuality();
            $this->em->persist($l);
            $this->licenses[strtolower($name)] = $l;
        }
        $this->em->flush();
    }

    public function getLicenses($raw_license)
    {
        $index = strtolower($raw_license);
        if (array_key_exists($index, $this->licenses)) {
            return $this->licenses[$index];
        } elseif (array_key_exists($index, $this->aliases)) {
            return $this->licenses[$this->aliases[$index]];
        } elseif (count($this->regex) > 0) {
            foreach($this->regex as $regex => $license) {
                if(preg_match($regex, $raw_license)) {
                    return $this->licenses[$license];
                }
            }
        }
        error_log('[' . date('d-M-Y H:i:s') . '] [Unknown license]' . $raw_license . "\n", 3, $this->log);

        return $this->licenses['unknown'];
    }

    private function _trim($value)
    {
        return trim($value, " \t\n\r\0\x0B\"'[]()&.");
    }
}
