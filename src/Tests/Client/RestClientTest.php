<?php

namespace Weather\Tests\Client;

use TestTools\TestCase\UnitTestCase;

class RestClientTest extends UnitTestCase
{
    public function testGetRequest()
    {
        $client = $this->get("rest.client");//Funktion aus UnitTestCase gibt restClient zurück über Container
        $result = $client->request("http://api.wunderground.com/api/cca50f0490f81828/astronomy/q/Australia/Melbourne.json");
        $this->assertInternalType('array', $result);//von phpunit geerbt, gibt Fehler, wenn result kein array ist
        $this->assertArrayHasKey('response', $result);
        $this->assertArrayHasKey('moon_phase', $result);
        $this->assertArrayHasKey('sun_phase', $result);
    }
}
