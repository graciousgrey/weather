<?php

namespace Weather\Form;

class WeatherQueryForm extends FormAbstract {
    protected function init(array $params = array())
    {
        //$this->setOptions(new \InputValidation\Options($this->getTranslator())); hätte ich nur gebraucht um auf country-Liste zu zugreifen

        $definition = array(
            'location' => array(
                'caption' => 'Location',
                'required' => true,
                'type' => 'string'
            ),
            'country' => array(
                'caption' => 'Country',
                'required' => true,
                'type' => 'string',
                'options' => [ //manuell statt counrty-Liste, diese passt nucht zur weather api
                    'Germany' => 'Deutschland',//man kann jetzt nur diese Länder auswählen
                    'Australia' => 'Australien',
                    'Austria' => 'Österreich'
                ]
            ),
        );

        $this->setDefinition($definition);
    }
}