<?php

namespace Odalisk\Scraper\Chicago;

use Symfony\Component\DomCrawler\Crawler;

use Odalisk\Scraper\BasePortal;

use Buzz\Message;

/**
 * The scraper for data.cityofchicago.org
 */
class ChicagoPortal extends BasePortal
{
    private static $criteria = array(
        'Creation' => '//span[@class="aboutCreateDate"]/span',
        'Description' => '//div[@class="aboutDataset"]/div[2]/div/p',
        'Last update' => '//span[@class="aboutUpdateDate"]/span',
        'Category' => '//div[@class="aboutDataset"]/div[4]/dl/dd[1]',
        'Tags' => '//div[@class="aboutDataset"]/div[4]/dl/dd[3]',
        'Permissions' => '//div[@class="aboutDataset"]/div[4]/dl/dd[2]',
        'Data Provider' => '//div[@class="aboutDataset"]/div[7]/dl/dd[1]',
        'Data Owner' => '//div[@class="aboutDataset"]/div[8]/dl/dd[1]/span',
        'Time Period' => '//div[@class="aboutDataset"]/div[8]/dl/dd[2]/span',
        'Frequency' => '//div[@class="aboutDataset"]/div[8]/dl/dd[3]/span',
        'Community Rating' => '//div[@class="aboutDataset"]/div[3]/dl/dd[1]/div',
    );

    protected static $datasets = array();

    // All dataset are available in this format
    // Moreover, use of javascript on the website, doesn't allow to get them automatically.
    private static $formats = array("CSV","XLS","XLSX","XML","JSON","RDF","RSS","PDF");

    private static $i = 0;

    public function __construct($buzz)
    {
        parent::__construct(
                $buzz
                , 'https://data.cityofchicago.org/'
                , 'http://data.cityofchicago.org/api/views/7eck-a4hy/rows.json'
            );
    }

    public function getDatasetsData()
    {
        return self::$datasets;
    }

    public function getDatasetsUrls()
    {
        // Get the paths
        $this->buzz->getClient()->setTimeout(20);
        $response = $this->buzz->get(
            $this->datasets_api_url,
            $this->buzzOptions
        );

        // Get the paths
        if (200 == $response->getStatusCode()) {
            $data = json_decode($response->getContent(), true);
            foreach ($data['data'] as $dataset) {
                self::$datasets[$this->getBaseUrl() . $dataset[10][0]] = null;
            }
        } else {
            throw new \RuntimeException('Couldn\'t fetch list of datasets');
        }


        return array_keys(self::$datasets);
    }

    public static function parseDataset(Message\Request $request, Message\Response $response)
    {
        $data = array(
            '#' => self::$i++,
            'url' => $request->getUrl(),
            'code' => $response->getStatusCode(),
        );


        if (200 == $data['code']) {
            $content = preg_replace("/((\n|\r|\t|\n\r)(\ )+)/","",$response->getContent());
            $crawler = new Crawler($content);
            if (0 == count($crawler)) {
                $data['empty'] = true;
            } else {

                #About section
                foreach (self::$criteria as $name => $path) {
                    $node = $crawler->filterXPath($path);

                    if (0 != count($node)) {
                       $data[$name] = $node->text();
                    }
                }
            }

                $data['Format'] = implode(" ", self::$formats);

        }
        self::$datasets[$data['url']] = $data;

        if (0 == self::$i % 100) {
           error_log('>>>> ' . self::$i . ' done, ' . count(self::$datasets) . ' to go.');
        }
    }

    public static function parseDatasetCriteria($datasetUrl, Message\Request $request, Message\Response $response)
    {
    }

    public function removeDataset($dataset)
    {
        unset(self::$datasets[$dataset]);
    }
}
