<?php

namespace Odalisk\Scraper\Tools\Normalize;

class FormatNormalizer
{
    /*
	private $replace = array(
		'/vnd.ms-excel|excel/'  => 'xls',
        '/htm/' => 'html',
		'/vnd.ms-word/' => 'doc',
		'/Otros|Unverified/' => 'unknown',
		'/image\/jpg|jpeg/' => 'jpg', // Aliases of jpg
		'/openDOCument.spreadsheet/' => 'ods',
		'/shp *' => 'shp'
	);
    */

    private $replace = array(
        'api' => 'unknown',
        'application/octet-stream' => 'unknown',
        'application/octet-stream+esri' => 'shp',
        'application/pdf' => 'pdf',
        'application/rdf+xml' => 'rdf',
        'application/rss+xml' => 'rss',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.ms-word' => 'doc',
        'application/xml+xls+pdf' => 'unknown',
        'application/x-msexcel' => 'xls',
        'application/xml' => 'xml',
        'application/zip' => 'zip',
        'aree' => 'unknown',
        'catálogos' => 'unknown',
        'comma separated variable (csv)' => 'csv',
        'csv file' => 'csv',
        'csv (zip)' => 'csv',
        'excel (xls)' => 'xls',
        'gpx' => 'unknown',
        'htm' => 'html',
        'html+rdfa' => 'html',
        'hoja de cálculo' => 'unknown',
        'imagen/texto' => 'unknown',
        'imagen' => 'jpeg',
        'image/jpeg' => 'jpeg',
        'jpeg' => 'jpeg',
        'mdb (zip)' => 'unknown',
        'other xml' => 'xml',
        'netcdf' => 'unknown',
        'otros' => 'unknown',
        'rar:shp' => 'shp',
        'shp (cc47)' => 'shp',
        'shp (l93)' => 'shp',
        'texto' => 'txt',
        'text/calendar' => 'ical',
        'text/csv' => 'csv',
        'text/html' => 'html',
        'text/plain' => 'txt',
        'text/sql' => 'sql',
        'text/tsv' => 'unknown',
        'text/xml' => 'xml',
        'text/rss-xml' => 'rss',
        'tms' => 'unknown',
        'unverified' => 'unknown',
        'word' => 'doc',
        'wmts' => 'unknown',
        'wsdl' => 'unknown',
        'zipped csv' => 'csv',
        '\.csv' => 'csv',
        '\.csv zipped' => 'csv',
        '\.xls' => 'xls',
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
            if(array_key_exists($format, $this->replace)) {
                $formats[$k] = $this->replace[$format];
            }
		}
		$formats = array_unique($formats);

		$result = array();
		foreach($formats as $format) {
            if(array_key_exists($format, $this->formats)) {
                $result[$format] = $this->formats[$format];
			} else {
                error_log('[Unknown file format ] ' . $format);
                $result['unknown'] = $this->formats['unknown'];
			}
		}
		//print_r($result);
        $result['raw'] = implode(', ', $formats);
		return($result);
	}

    private function _trim($value)
    {
        return trim($value, " \t\n\r\0\x0B\"'[]&.");
    }
}
