<?php

namespace Weather\Client;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Monolog\Logger;
use Weather\Exception\Exception;
use Weather\Exception\NotFoundException;

class WundergroundClient
{
    private $client; // Encapsulation
    private $apikey;
    private $cache;
    private $logger;

    public function __construct(RestClient $restClient, string $apikey) //Dependency Injection - RestClient kommt über Depency Injection Container, der vom UnitTest gebildet wird
    {
        $this->client = $restClient;
        $this->apikey = $apikey;
    }

    public function setCache(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    protected function getCache():AdapterInterface
    {
        if (empty($this->cache)) {
            throw new Exception('Cache not set');
        }
        return $this->cache;
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    protected function getLogger(): Logger
    { // :Logger definiert Typ Logger für return value
        if (empty($this->logger)) {
            throw new Exception('Logger not set');
        }
        return $this->logger;
    }

    public function log(string $level, string $message)//level z.B. error, debug, info etc
    {
        try {
            $this->getLogger()->log($level, $message);//Funktionen von Logger Interface
        } catch (Exception $e) { // unterdrückt Exception, weil uns das egal ist
            // do nothing
        }
    }

    protected function performRequest($type, $location, $country)
    {
        $url = "http://api.wunderground.com/api/$this->apikey/$type/q/$country/$location.json";

        $cache = $this->getCache();
        $cacheResult = $cache->getItem(md5($url));

        if ($cacheResult->isHit()) {
            $result = $cacheResult->get();
            $this->log('debug', 'Cache hit for ' . $url);
        } else {
            $result = $this->client->request($url);
            $cacheResult->set($result);
            $cache->save($cacheResult);
            $this->log('debug', 'Saved ' . $url . ' to cache');
        }

        if(isset($result['response']['error'])) {
            throw new NotFoundException($result['response']['error']['description']);
        }

        return $result;
    }

    public function getSunPhase($location, $country)
    {
        $response = $this->performRequest('astronomy', $location, $country);
        $result = $response['sun_phase'];

        return $result;
    }

    public function getConditions($location, $country)
    {
        $response = $this->performRequest('conditions', $location, $country);

        $result = array(
            'location' => $response['current_observation']['display_location']['full'],
            'current_observation' => array(
                'temp_c' => $response['current_observation']['temp_c'],
                'relative_humidity' => $response['current_observation']['relative_humidity'],
                'feelslike_c' => $response['current_observation']['feelslike_c'],
                'image' =>array(
                    'title' => $response['current_observation']['image']['title'],
                    'url' => $response['current_observation']['image']['url'],
                    'link' => $response['current_observation']['image']['link']
                ),
                'icon_url' => $response['current_observation']['icon_url']
            )
        );

        return $result;
    }

    public function getForecast($location, $country): array
    {
        $response = $this->performRequest('forecast', $location, $country);

        $result = array();

        foreach ($response['forecast']['simpleforecast']['forecastday'] as $key => $forecast) { // alle arrays oder keys, die
            //direkt  innerhalb von $response['forecast']['simpleforecast']['forecastday'] vorkommen (hier 0,1,2,3) werden als
            //forecast bezeichnet.
            $result[$key]['high'] = $forecast['high']['celsius']; // array high mit values für key celcius für alle dates
            $result[$key]['low'] = $forecast['low']['celsius'];
            $result[$key]['conditions'] = $forecast['conditions'];
            $result[$key]['icon_url'] = $forecast['icon_url'];
            $result[$key]['date'] = $forecast['date'];
        }

        return $result;
    }

    public function getAll($location, $country): array
    {
        $result = array(
            'sun_phase' => $this->getSunPhase($location, $country),
            'conditions' => $this->getConditions($location, $country),
            'forecast' => $this->getForecast($location, $country)
        );

        return $result;
    }
}