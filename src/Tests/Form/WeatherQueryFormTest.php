<?php

namespace Weather\Tests\Form;

use TestTools\TestCase\UnitTestCase;

class WeatherQueryFormTest extends UnitTestCase
{
    /**
     * @var \Weather\Form\UserForm
     */
    protected $form;

    public function setUp()
    {
        $this->form = $this->get('form.weatherquery');
        //$this->form = $this->get('form.factory')->getForm('WeatherQuery'); das gleiche wie oben
    }

    public function testValidForm()
    {
        $inputValues = array(
            'location' => 'Berlin',
            'country' => 'Germany',

        );

        $this->form->setDefinedWritableValues($inputValues); //Daten aus Array werden form zugewiesen

        $this->form->validate();//checkt ob definition aus weatherqueryform erfüllt ist

        // print_r($this->form->getErrorsAsText());

        $this->assertFalse($this->form->hasErrors());
        $this->assertCount(0, $this->form->getErrors());

    }

    public function testInvalidForm()
    {
        $inputValues = array(
            'location' => '',
            'country' => 'Catland'
        );

        $this->form->setDefinedWritableValues($inputValues);

        $this->form->validate();

        $this->assertTrue($this->form->hasErrors());

        $this->assertCount(2, $this->form->getErrors());
    }
}