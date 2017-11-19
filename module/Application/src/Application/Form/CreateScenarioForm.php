<?php

namespace Application\Form;

use Zend\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class CreateScenarioForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->addElements();
    }

    public function addElements()
    {
        // File Input
        $name = new Element\Text('scenario-name');
        $name->setLabel('New scenario name: ')
             ->setAttribute('id', 'scenario-name');
        $this->add($name);
    }


}