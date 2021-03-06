<?php

namespace Application\Form;
use Zend\Form\Form;
 
class UlazniPodaciInputForm extends Form{
    
    public function __construct() {
         
        parent::__construct();	
				
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'oznakaElaborata',
		'attributes' =>  array(
			'id' => 'oznakaElaborata',
			'placeholder' => 'Oznaka elaborata',
			'pattern' => '[A-Za-z]{3}'
		)
		));	
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'podrucniUred',
		'attributes' =>  array(
			'id' => 'podrucniUred',
			'placeholder' => 'Područni ured za katastar'
		)
		));		
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'katastarskaOpcina',
		'attributes' =>  array(
			'id' => 'katastarskaOpcina',
			'placeholder' => 'Katastarska općina'
		)
		));		
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'brojLista',
		'attributes' =>  array(
			'id' => 'brojLista',
			'placeholder' => 'Broj detaljnog lista'
		)
		));		
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'zemljiOpcina',
		'attributes' =>  array(
			'id' => 'zemljiOpcina',
			'placeholder' => 'Zemljišnoknjižna općina'
		)
		));				
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'kontaktOsoba',
		'attributes' =>  array(
			'id' => 'kontaktOsoba',
			'style' => 'width: 135%',
			'placeholder' => 'Kontakt osoba (izdavatelj elaborata)'
		)
		));
		$this->add(array(     
		'type' => 'Zend\Form\Element\Date',
		'name' => 'datumElaborata',
		'attributes' =>  array(
			'id' => 'datumElaborata',
			'style' => 'width: 135%',
			'placeholder' => 'Datum elaborata'
		)
		));
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',
		'name' => 'mjestoElaborata',
		'attributes' =>  array(
			'id' => 'mjestoElaborata',
			'style' => 'width: 135%',
			'placeholder' => 'Mjesto elaborata'
		)
		));	


		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'ime1',
		'attributes' =>  array(
			'id' => 'ime1',
			'placeholder' => 'Ime'
		)
		));		
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'prezime1',
		'attributes' =>  array(
			'id' => 'prezime1',
			'placeholder' => 'Prezime'
		)
		));		
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'OIB1',
		'attributes' =>  array(
			'id' => 'OIB1',
			'placeholder' => 'OIB'
		)
		));		
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'adresa1',
		'attributes' =>  array(
			'id' => 'adresa1',
			'placeholder' => 'Adresa'
		)
		));


		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'brojKatCes1',
		'attributes' =>  array(
			'id' => 'brojKatCes1',
			'placeholder' => 'Broj katastarske čestice'
		)
		));
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'predNositeljPrava-1-1',
		'attributes' =>  array(
			'id' => 'predNositeljPrava-1-1',
			'placeholder' => 'Nositelj prava 1',
			'style' => 'width: 80%; display: inline-block;'
		)
		));
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'predNositeljPravaDva1',
		'attributes' =>  array(
			'id' => 'predNositeljPravaDva1',
			'placeholder' => 'Nositelj prava 2',
			'style' => 'width: 200%'
		)
		));
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'predNositeljPravaTri1',
		'attributes' =>  array(
			'id' => 'predNositeljPravaTri1',
			'placeholder' => 'Nositelj prava 3',
			'style' => 'width: 200%'
		)
		));
		$this->add(array(     
		'type' => 'Zend\Form\Element\Textarea',       
		'name' => 'cesticaOmedenja',
		'attributes' =>  array(
			'id' => 'cesticaOmedenja',
			'placeholder' => 'Sažetak omeđenja:',
			'style' => 'width: 100%; margin-top: 2em;',
			'rows' => '12',
			'cols' => '80'
		)
		));


		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'brojSusjedKatCes1',
		'attributes' =>  array(
			'id' => 'brojSusjedKatCes1',
			'placeholder' => 'Broj katastarske čestice',
			'style' => 'width: 25%;'
		)
		));
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',
		'name' => 'susNositeljPrava-1-1',
		'attributes' =>  array(
			'id' => 'susNositeljPrava-1-1',
			'placeholder' => 'Nositelj prava 1',
			'style' => 'width: 70%;'
		)
		));
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'susNositeljPravaDva1',
		'attributes' =>  array(
			'id' => 'susNositeljPravaDva1',
			'placeholder' => 'Nositelj prava 2',
			'style' => 'width: 100%;'
		)
		));
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'susNositeljPravaTri1',
		'attributes' =>  array(
			'id' => 'susNositeljPravaTri1',
			'placeholder' => 'Nositelj prava 3',
			'style' => 'width: 100%;'
		)
		));
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'susNositeljPravaCetiri1',
		'attributes' =>  array(
			'id' => 'susNositeljPravaCetiri1',
			'placeholder' => 'Nositelj prava 4',
			'style' => 'width: 100%;'
		)
		));


		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'dokumentNaziv1',
		'attributes' =>  array(
			'id' => 'dokumentNaziv1',
			'placeholder' => 'Naziv'
		)
		));
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'dokumentKlasa1',
		'attributes' =>  array(
			'id' => 'dokumentKlasa1',
			'placeholder' => 'Klasa'
		)
		));
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'dokumentUrbroj1',
		'attributes' =>  array(
			'id' => 'dokumentUrbroj1',
			'placeholder' => 'Urudžbeni broj'
		)
		));
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'dokumentIzdano1',
		'attributes' =>  array(
			'id' => 'dokumentIzdano1',
			'placeholder' => 'Izdano od'
		)
		));
		$this->add(array(     
		'type' => 'Zend\Form\Element\Text',       
		'name' => 'dokumentDatum1',
		'attributes' =>  array(
			'id' => 'dokumentDatum1',
			'placeholder' => 'Datum'
		)
		));

		
    }    
}