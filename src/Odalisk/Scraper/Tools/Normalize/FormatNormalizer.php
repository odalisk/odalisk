<?php

namespace Odalisk\Scraper\Tools\Normalize;

class FormatNormalizer
{
	private $replace = array(
		'/vnd.ms-excel|excel/'  => 'xls',
        '/htm/' => 'html',
		'/vnd.ms-word/' => 'doc',
		'/Otros|Unverified/' => 'unknown',
		'/image\/jpg|jpeg/' => 'jpg', // Aliases of jpg
		'/openDOCument.spreadsheet/' => 'ods',
		'/shp.*/' => 'shp', // strip (C99) or (LP *)
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
		$formats = array_unique(preg_split('/;/', strtolower($raw_formats)));
		foreach($formats as $k => $format) {
			$format = $this->_trim($format);
			foreach($this->replace as $bad => $good) {	
                $format = preg_replace($bad, $good, $format);      
			}

			$formats[$k] = $format;
		}
		$formats = array_unique($formats);

		$result = array();
		foreach($formats as $format) {
            if(array_key_exists($format, $this->formats)) {
                $result[$format] = $this->formats[$format];
			} else {
                error_log('Format inconnu ! => '.$format);
                $result[] = $this->formats['unknown'];
			}
		}
		//print_r($result);
        $result['raw'] = implode(', ', $formats);
		return($result);
	}

    private function _trim($value)
    {
        return trim($value, " \t\n\r\0\x0B\"'[]()&.");
    }
}
