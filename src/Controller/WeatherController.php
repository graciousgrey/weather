<?php

namespace Weather\Controller;

use Symfony\Component\HttpFoundation\Request;
use Weather\Client\WundergroundClient;
use Weather\Form\WeatherQueryForm;

class WeatherController
{
    private $client;
    private $form;

    public function __construct(WundergroundClient $wundergroundClient, WeatherQueryForm $form)
    {
        $this->client = $wundergroundClient;
        $this->form = $form;
    }

    public function indexAction(Request $request) //Framework speichert Request automatisch
    {
        $location = $request->query->get('location', "Berlin"); // query Objekt im Request Objekt wird geöffnet und location value
        // als $location gespeichert, falls location nicht gesetzt ist Berlin default value
        $country = $request->query->get('country', 'Germany');

        $result = $this->client->getAll($location, $country);


        return $result;
    }
}

