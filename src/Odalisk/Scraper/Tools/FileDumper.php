<?php

namespace Odalisk\Scraper\Tools;

use Odalisk\Entity\DatasetCrawl;

use Buzz\Message;

class FileDumper {
    /**
     * The doctrine handle
     */
    protected static $doctrine;
    
    /**
     * Entity manager
     */
    protected static $em;
    
    protected static $count = 0;
    protected static $total_count = 0;
    
    protected static $mapping = array();
    
    protected static $base_path;
    
    public static function saveToDisk(Message\Request $request, Message\Response $response) {
        self::$count++;
        //error_log(self::$count . ' > ' . memory_get_usage(true));
        
        $code = $response->getStatusCode();
        $url = $request->getUrl();
        $hash = md5($url);
        $platform = self::getPlatformName($url);
        
        $crawl = new DatasetCrawl($url, $hash, $code, self::$mapping[$platform]['portal']);
        self::$em->persist($crawl);
        
        if(200 == $code) {
            file_put_contents(self::$base_path . $platform . '/' . $hash, $response->getContent());
        }
        
        if(0 == self::$count % 100) {
           error_log('> ' . self::$count . ' / ' . self::$total_count . ' done');
           error_log('> ' . memory_get_usage(true) / (1024 * 1024));
        }
        
        if(self::$count == self::$total_count || self::$count % 1000 == 0) {
            error_log('Flushing data !');
            self::$em->flush();
        }
    }

	public static function saveUrls($urls, $portal_name) {
		self::verifyPortalPath($portal_name);
		$file = self::$base_path.$portal_name.'/urls.json';
		file_put_contents($file, json_encode($urls));
	}

	public static function getUrls($portal_name) {
		$file = self::$base_path.$portal_name.'/urls.json';
		return(json_decode(file_get_contents($file), true));
	}

	public static function verifyPortalPath($portal_name) {
        $path = self::$base_path . $portal_name;
        if(! is_dir($path)) {
            mkdir($path, 0755, TRUE);
        }
	}
    
    public static function setBasePath($path) {
        self::$base_path = $path;
    }
    
    public static function setTotalCount($count) {
        self::$total_count = $count;
    }
    
    public static function getTotalCount() {
        return self::$total_count;
    }
    
    public static function addMapping($name, $url, $portal) {
        self::$mapping[$name] = array('url' => $url, 'portal' => $portal);
		self::verifyPortalPath($name);
    }
    
    public static function getPlatformName($dataset_url) {
        foreach(self::$mapping as $name => $data) {
            if(0 === strpos($dataset_url, $data['url'])) {
                return $name;
            }
        }
    }
    
    public static function setDoctrine($doctrine) {
        self::$doctrine = $doctrine;
        self::$em = self::$doctrine->getEntityManager();
        self::$em->getConnection()->getConfiguration()->setSQLLogger(null);
    }
}
