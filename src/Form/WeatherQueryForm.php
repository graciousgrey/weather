<?php

namespace Weather\Form;

class WeatherQueryForm extends FormAbstract {
    protected function init(array $params = array())
    {
        //$this->setOptions(new \InputValidation\Options($this->getTranslator())); hätte ich nur gebraucht um auf country-Liste zu zugreifen

        $definition = array(
            'location' => array(
                'caption' => 'Location',
                'required' => false,
                'type' => 'string',
                'default' => 'Berlin'
            ),
            'country' => array(
                'caption' => 'Country',
                'required' => false,
                'type' => 'string',
                'options' => $this->options("weather_countries"),
                'default' => 'Germany'
            ),
        );

        $this->setDefinition($definition);
    }
}