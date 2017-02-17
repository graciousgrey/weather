<?php

namespace Weather\Tests\Client;

use TestTools\TestCase\UnitTestCase;
use Weather\Client\WundergroundClient;

class WundergroundClientTest extends UnitTestCase
{
    /**
     * sagt PHP, dass getClient den Abruf des Clients aus dem Container kapselt und durch @ return mitteilt um welche Klasse es
     * sich handelt
     *
     *
     * @return WundergroundClient
     */
    protected function getClient(): WundergroundClient
    {
        $container = $this->getContainer();
        $result = $container->get('wunderground.client');
        $result->setCache($container->get('cache'));
        return $result;
    }

    public function testGetSunPhase()
    {
        $client = $this->getClient();
        $result = $client->getSunPhase('Sydney', 'Australia');
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('sunrise', $result);
        $this->assertArrayHasKey('sunset', $result);
    }

    public function testGetConditions()
    {
        $client = $this->getClient();
        $result = $client->getConditions('Sydney', 'Australia');
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('location', $result);
        $this->assertArrayHasKey('current_observation', $result);

    }

    public function testForecast()
    {
        $client = $this->getClient();
        $result = $client->getForecast('Sydney', 'Australia');
        $this->assertInternalType('array', $result);

        foreach ($result as $key => $forecast) { // each $date innerhalb von $result wird als forecast bezeichnet
            $this->assertArrayHasKey('high', $forecast); // schaut ob in jedem forecast key high vorkommt
            $this->assertArrayHasKey('low', $forecast);
            $this->assertArrayHasKey('conditions', $forecast);
            $this->assertArrayHasKey('date', $forecast);
        }

    }

    public function testGetAllWithCache() {
        $client = $this->getClient();
        $result = $client->getAll('Sydney', 'Australia');
        $this->assertInternalType('array', $result);

        $this->assertArrayHasKey('sun_phase', $result);
        $this->assertArrayHasKey('conditions', $result);
        $this->assertArrayHasKey('forecast', $result);
        print_r($result);

    }

    public function testGetAllWithLogger() {
        $client = $this->getClient();
        $container = $this->getContainer(); //sonst hätte ich 3 Container
        $logger = $container->get('log');
        $logHandler = $container->get('log.handler');
        $client->setLogger($logger);
        $result = $client->getAll('Sydney', 'Australia');
        $this->assertInternalType('array', $result);

        $this->assertArrayHasKey('sun_phase', $result);
        $this->assertArrayHasKey('conditions', $result);
        $this->assertArrayHasKey('forecast', $result);

        $logHandler->hasRecordThatContains('sun_phase', 'debug');

    }
}