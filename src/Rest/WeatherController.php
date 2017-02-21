<?php

namespace Weather\Rest;

use Symfony\Component\HttpFoundation\Request;
use Weather\Client\WundergroundClient;
use Weather\Exception\FormInvalidException;
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

    public function cgetAction(Request $request) //Framework speichert Request automatisch
    {
        // query gibt URL Parameter (Get) zurück, für method Post braucht man request statt query
        $this->form->setDefinedWritableValues($request->query->all());
        $this->form->validate(); // Validation

        if ($this->form->hasErrors()) {
            throw new FormInvalidException($this->form->getFirstError());
        }

        try {
            //magic __get function aus FormAbstract wird genutzt (form->)
            $result = array($this->client->getAll($this->form->location, $this->form->country));
        } catch (NotFoundException $exception) {
            $result = array(); //array nötig weil wir 0 - viele Ergebnisse im result rray haben wollen (also theoretisch,
            //hier nicht)
        }

        return $result;
    }
}

