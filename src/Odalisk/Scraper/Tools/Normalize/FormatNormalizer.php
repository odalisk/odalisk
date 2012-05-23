<?php

namespace Odalisk\Scraper\Tools\Normalize;

class FormatNormalizer
{
    private $formats = array();
    private $aliases = array();

    /**
     * Issue : how classify file types like : zip (xls, doc, pdf) ? Is it good to
     * deliver a zip ? Do we say that the dataset is disponible in 3 formats
     * even if we need to download a zip ?
     * For now, we say the file format is zip
     */
    private $replace = array(
        '/^application\//' => '',
        '/^image\//' => '',
        '/^text\//' => '',
        '/^zip\//' => '',
        '/[+-]xml$/' => '',
        '/\./' => '',
        '/zip (.*)/' => 'zip',
    );

    
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
            $f->setAliases($data['aliases']);
            foreach($data['aliases'] as $alias) {
                $this->aliases[$alias] = $format;
            }
			$f->setIsOpen($data['is_open']);
			$f->setHasSpec($data['has_spec']);
			$f->setIsComputerReadable($data['is_computer_readable']);

            $this->em->persist($f);
            $this->formats[$format] = $f;
        }
		$this->em->flush();
    }
    
	public function getFormats($raw_formats) {
		$formats = array_unique(preg_split('/[;,&]/', strtolower($raw_formats)));
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
            } elseif(array_key_exists($format, $this->aliases)) {
                $result[$this->aliases[$format]] = $this->formats[$this->aliases[$format]];
			} else {
                error_log('[Unknown file format ] '.$format);
                $result['unknown'] = $this->formats['unknown'];
			}
		}
		//print_r($result);
        $result['raw'] = implode(', ', $formats);
		return($result);
	}

    private function _trim($value)
    {
        //return(trim($value));
        return trim($value, " \t\n\r\0\x0B\"'[]()&.");
    }
}
