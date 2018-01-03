<?php

namespace Application\Form;
use Zend\Form\Form;
 
class UlazniPodaciInputForm extends Form{
    
    public function __construct() {
         
        parent::__construct();
		
		$this->add(array(     
		'type' => 'Zend\Form\Element\Select',       
		'name' => 'ulazni1',
		'attributes' =>  array(
			'id' => 'ulazni1',                
			'options' => array(
				'elaborat-1' => '1. diobe ili spajanja katastarskih čestica',
				'elaborat-2' => '2. provedbe dokumenata ili akata prostornog uređenja',
				'elaborat-3' => '3. evidentiranja pomorskog ili vodnog dobra',
				'elaborat-4' => '4. evidentiranja, brisanja ili promjene podataka o zgradama ili drugim građevinama',
				'elaborat-5' => '5. evidentiranja ili promjene podataka o načinu uporabe katastarskih čestica',
				'elaborat-6' => '6. evidentiranja stvarnog položaja pojedinačnih već evidentiranih katastarskih čestica',
				'elaborat-7' => '7. evidentiranja međa uređenih u posebnome postupku',
				'elaborat-8' => '8. provedbe u zemljišnoj knjizi',
				'elaborat-9' => '9. izmjere postojećeg stanja radi ispravljanja zemljišne knjige',
				'elaborat-10' => '10. ostalo',
			),
		),
		));		
		
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'ulazni2',
		'attributes' =>  array(
			'id' => 'ulazni2',
			'placeholder' => 'Upišite ime elaborata',
			'style' => 'display: none;'
		)
		));

		
    }    
}