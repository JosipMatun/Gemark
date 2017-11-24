<?php

namespace Application\Form;
use Zend\Form\Form;
 
class DisplayScenarioInputForm extends Form{
    
    public function __construct( $allVariablesUnique = null, $scenarioName = null) {
         
        parent::__construct($name);
		
		 foreach ($allVariablesUnique as $variable) {
			$this->add(array(
			'type' => 'Zend\Form\Element\Text',
			'name' => $variable,
			'attributes' => array(
					'id' => $variable,
					'name' => $variable
			)
			));
		 }
		 
		 $this->add(array(
		 'type' => 'Zend\Form\Element\Hidden',
		 'name' => 'scenario-name',
		 'attributes' => array(
		 		'value' => $scenarioName
		 )
		 ));

    }    
}