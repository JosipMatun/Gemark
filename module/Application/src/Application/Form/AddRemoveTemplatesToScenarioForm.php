<?php

namespace Application\Form;
use Zend\Form\Form;
 
class AddRemoveTemplatesToScenarioForm extends Form{
    
    public function __construct( $scenarioName = null) {
        
        parent::__construct($name);
        
		$filelist = glob("data/templates/*");
		
        $this->add(array(
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'name' => 'templates',
            'options' => array(
                'value_options' => $filelist,
            ),
            //'attributes' => array(
            //    'value' => '1'
            //)
        ));
		 $this->add(array(
		 'type' => 'Zend\Form\Element\Hidden',
		 'name' => 'scenario-name',
		 'attributes' => array(
		 		'value' => $scenarioName
		 )
		 ));
    }    
}