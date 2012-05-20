<?php

namespace Odalisk\Scraper\Tools;

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
    protected static $totalCount = 0;

    protected static $mapping = array();

    protected static $base_path;

    public static function saveToDisk(Message\Request $request, Message\Response $response) {
        self::$count++;

        $file = array();
        $file['meta']['code'] = $response->getStatusCode();
        $file['meta']['url'] = $request->getUrl();
        $file['meta']['hash'] = md5($file['meta']['url']);

        if (200 == $file['meta']['code']) {
            $file['content'] = $response->getContent();
        } else {
            $file['content'] = "";
        }

        $platform = self::getPlatformName($file['meta']['url']);

        file_put_contents(self::$base_path . $platform . '/' . $file['meta']['hash'], json_encode($file));

        if (0 == self::$count % 100 || self::$count == self::$totalCount) {
           error_log('[Get HTML] ' . self::$count . ' / ' . self::$totalCount . ' done');
           error_log('[Get HTML] currently using ' . memory_get_usage(true) / (1024 * 1024) . 'MB of memory');
        }
    }

    public static function saveUrls($urls, $portal_name) {
        self::verifyPortalPath($portal_name);
        $file = self::$base_path.$portal_name.'/urls.json';
        file_put_contents($file, json_encode($urls));
    }

    public static function getUrls($portal_name) {
        $file = self::$base_path.$portal_name.'/urls.json';
        $data = file_get_contents($file);

        if (false === $data) {
            error_log('[Get HTML] URL file is missing. Run ./console odalisk:geturls ' . $portal_name);

            return array();
        } else {
           return json_decode($data, true);
        }
    }

    public static function verifyPortalPath($portal_name) {
        $path = self::$base_path . $portal_name;
        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    public static function setBasePath($path) {
        self::$base_path = $path;
    }

    public static function setTotalCount($count) {
        self::$totalCount = $count;
    }

    public static function getTotalCount() {
        return self::$totalCount;
    }

    public static function addMapping($name, $url, $portal) {
        self::$mapping[$name] = array('url' => $url, 'portal' => $portal);
        self::verifyPortalPath($name);
    }

    public static function getPlatformName($dataset_url) {
        foreach (self::$mapping as $name => $data) {
            if (0 === strpos($dataset_url, $data['url'])) {
                return $name;
            }
        }
        error_log('[FileDumper] No match found for : ' . $dataset_url);
    }

    public static function setDoctrine($doctrine) {
        self::$doctrine = $doctrine;
        self::$em = self::$doctrine->getEntityManager();
        self::$em->getConnection()->getConfiguration()->setSQLLogger(null);
    }
}
