<?php

namespace Weather\Form;

class UserForm extends FormAbstract {
    protected function init(array $params = array())
    {
        $definition = array(
            'firstname' => array('caption' => 'First Name', 'required' => true, 'type' => 'string'),
            'lastname' => array('caption' => 'Last Name', 'required' => true, 'type' => 'string'),
            'email' => array('caption' => 'E-Mail', 'required' => true, 'type' => 'email'),
            'admin' => array('caption' => 'Admin', 'optional' => true, 'type' => 'bool')

        );

        $this->setDefinition($definition);
    }
}