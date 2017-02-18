<?php

namespace Weather\Controller;

use Symfony\Component\HttpFoundation\Request;
use Weather\Client\WundergroundClient;
use Weather\Exception\NotFoundException;
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
        // query gibt URL Parameter (Get) zurück, für method Post braucht man request statt query
        $this->form->setDefinedWritableValues($request->query->all());
        $this->form->validate(); // Validation

        if ($this->form->hasErrors()) {
            return '/weather/invalid';
        }

        try {
            //magic __get function aus FormAbstract wird genutzt (form->)
            $result = $this->client->getAll($this->form->location, $this->form->country);
        } catch (NotFoundException $exception) {
            return '/weather/notfound';
        }


        return $result;
    }

    public function notfoundAction()
    {

    }

    public function invalidAction()
    {

    }
}

