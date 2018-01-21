<?php

namespace Application\Form;
use Zend\Form\Form;
 
class VrstaElaborataInputForm extends Form{
    
    public function __construct() {
         
        parent::__construct();
		
		$this->add(array(     
		'type' => 'Zend\Form\Element\Select',       
		'name' => 'usernames',
		'attributes' =>  array(
			'id' => 'usernames',                
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
		'name' => 'ostalo-ime-elaborata',
		'attributes' =>  array(
			'id' => 'ostalo-ime-elaborata',
			'placeholder' => 'Upišite ime elaborata',
			'style' => 'display: none;'
		)
		));

		$this->add(array(
       'type' => 'Zend\Form\Element\MultiCheckbox',
       'name' => 'sastavni-dijelovi',
       'options' => array(
           'value_options' => array(
               array(
                   'value' => 'sadrzaj-1',
                   'label' => ' 1. Naslovna stranica',
                   'selected' => true,
                   'attributes' => array(
                       'id' => 'sadrzaj-1'
                   )
               ),
               array(
                   'value' => 'sadrzaj-2',
                   'label' => ' 2. Skica izmjere',
                   'selected' => true,
                   'attributes' => array(
                       'id' => 'sadrzaj-2'
                   )
               ),
               array(
                   'value' => 'sadrzaj-3',
                   'label' => ' 3. popis koordinata',
                   'selected' => true,
                   'attributes' => array(
                       'id' => 'sadrzaj-3'
                   )
               ),
               array(
                   'value' => 'sadrzaj-4',
                   'label' => ' 4. prikaz izmjerenog stanja ili situacija',
                   'selected' => true,
                   'attributes' => array(
                       'id' => 'sadrzaj-4'
                   )
               ),
               array(
                   'value' => 'sadrzaj-5',
                   'label' => ' 5. iskaz površina',
                   'selected' => true,
                   'attributes' => array(
                       'id' => 'sadrzaj-5'
                   )
               ),
               array(
                   'value' => 'sadrzaj-6',
                   'label' => ' 6. prijavni list za katastar',
                   'selected' => true,
                   'attributes' => array(
                       'id' => 'sadrzaj-6'
                   )
               ),
               array(
                   'value' => 'sadrzaj-7',
                   'label' => ' 7. kopija katastarskog plana za katastar',
                   'selected' => true,
                   'attributes' => array(
                       'id' => 'sadrzaj-7'
                   )
               ),
               array(
                   'value' => 'sadrzaj-8',
                   'label' => ' 8. izvješća o izrađenom elaboratu',
                   'selected' => true,
                   'attributes' => array(
                       'id' => 'sadrzaj-8'
                   )
               ),
               array(
                   'value' => 'sadrzaj-8-1',
                   'label' => ' 8.1 tehničko izvješće',
                   'selected' => true,
                   'attributes' => array(
                       'id' => 'sadrzaj-8-1',
                       'style' => 'margin-left: 1em;'
                   )
               ),
               array(
                   'value' => 'sadrzaj-8-2',
                   'label' => ' 8.2 izvješće o zgradama i drugim građevinama',
                   'selected' => true,
                   'attributes' => array(
                       'id' => 'sadrzaj-8-2',
                       'style' => 'margin-left: 1em;'
                   )
               ),
               array(
                   'value' => 'sadrzaj-8-3',
                   'label' => ' 8.3 izvješće o međama i drugim zgradama te o novom razgraničenju',
                   'selected' => true,
                   'attributes' => array(
                       'id' => 'sadrzaj-8-3',
                       'style' => 'margin-left: 1em;'
                   )
               ),
               array(
                   'value' => 'sadrzaj-9',
                   'label' => ' 9. prijavni list za zemljišnu knjigu',
                   'selected' => true,
                   'attributes' => array(
                       'id' => 'sadrzaj-9'
                   )
               ),
               array(
                   'value' => 'sadrzaj-10',
                   'label' => ' 10. kopija katastarskog plana za katastar',
                   'selected' => true,
                   'attributes' => array(
                       'id' => 'sadrzaj-10'
                   )
               )
           ),
       ),
   ));

    }    
}