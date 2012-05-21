<?php

namespace Odalisk\Scraper\Tools\Normalize;

class FormatNormalizer
{
	private $replace = array(
		'/vnd.ms-excel|excel/'  => 'xls',
		'/htm/' => 'html',
		'/vnd.ms-word/' => 'doc',
		'/Otros|Unverified/' => 'Unknown', 
		'/image\/jpg/' => 'jpg',
		'/openDOCument.spreadsheet/' => 'ods',
		'/shp.*/' => 'shp'
	);

    private $formats = array();
    
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getEntityManager();
		$this->er = $this->em->getRepository('Odalisk\Entity\Format');
    }
    
    public function init($yaml) {
        foreach($yaml as $format => $data) {
			$f = $this->er->findOneByFormat($format);
			if(!$f) {
				$f = new \Odalisk\Entity\Format($format);
			}
			$f->setIsOpen($data[0]);
			$f->setHasSpec($data[1]);
			$f->setIsComputerReadable($data[2]);

            $this->em->persist($f);
            $this->formats[$format] = $f;
        }
		$this->em->flush();
    }
    
	public function getFormats($raw_formats) {
		// error_log("raw_formats : $raw_formats");
		$formats = array_unique(preg_split('/;/', strtolower($raw_formats)));
		foreach($formats as $k => $format) {
			$formats[$k] = $this->_trim($format);
			foreach($this->replace as $bad => $good) {
                $formats[$k] = preg_replace($bad, $good, $format);
			}
		}
		$formats = array_unique($formats);

		$result = array();
		foreach($formats as $format) {
            if(array_key_exists($format, $this->formats)) {
                $result[$format] = $this->formats[$format];
			} else {
                $result['unknown'] = $this->formats['unknown'];
			}
		}
		return $result;
	}

    private function _trim($value)
    {
        return trim($value, " \t\n\r\0\x0B\"'[]()&.");
    }
}
