<?php

namespace Weather\Tests\Controller;

use TestTools\TestCase\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;

class WeatherControllerTest extends UnitTestCase {

    public function testWeatherController() {
        $controller = $this->get("controller.web.weather");
        $request = Request::create('http://localhost-debug:8080/weather?location=Hamburg');
        $result = $controller->indexAction($request);
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('sun_phase', $result);
        $this->assertArrayHasKey('conditions', $result);
        $this->assertArrayHasKey('forecast', $result);
    }
}
