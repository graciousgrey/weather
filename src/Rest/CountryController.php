<?php

namespace Weather\Rest;

use Symfony\Component\HttpFoundation\Request;
use Weather\Client\WundergroundClient;
use Weather\Exception\FormInvalidException;
use Weather\Exception\NotFoundException;
use Weather\Form\WeatherQueryForm;

class CountryController
{
    private $client;
    private $form;

    public function __construct(WundergroundClient $wundergroundClient, WeatherQueryForm $form)
    {
        $this->client = $wundergroundClient;
        $this->form = $form;
    }

    public function getLocationAction($country, $location) // Parameter kommen vom Router
    {
        $this->form->location = $location;
        $this->form->country = $country;
        $this->form->validate(); // Validation

        if ($this->form->hasErrors()) {
            throw new FormInvalidException($this->form->getFirstError());
        }

        //magic __get function aus FormAbstract wird genutzt (form->)
        $result = $this->client->getAll($this->form->location, $this->form->country);

        return $result;
    }
}

