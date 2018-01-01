<?php

namespace Application\Form;
use Zend\Form\Form;
 
class ElaboratDefinitionDisplayForm extends Form{
    
    public function __construct( $allVariablesUnique = null, $scenarioName = null) {
         
        parent::__construct($name);
		 
		 $this->add(array(
		 'type' => 'Zend\Form\Element\Hidden',
		 'name' => 'scenario-name',
		 'attributes' => array(
		 		'value' => $scenarioName
		 )
		 ));

    }    
}