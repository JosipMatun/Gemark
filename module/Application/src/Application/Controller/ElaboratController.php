<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Session\Container;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Http\Request;
use Zend\Filter\File\RenameUpload;
use Zend\Filter\Compress;

use Application\Form\UploadForm;
use Application\Form\CreateScenarioForm;
use Application\Form\AddRemoveTemplatesToScenarioForm;
use Application\Form\DisplayScenarioInputForm;
use Application\Form\VrstaElaborataInputForm;
use Application\Form\ElaboratDefinitionDisplayForm;
use Application\Form\UlazniPodaciInputForm;
use Application\Form\PrijavniListZaKatastarInputForm;

class ElaboratController extends AbstractActionController
{
	protected $elaboratTable;
	
public function indexAction()
    {
    	// Creating the new document...
$phpWord = new \PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

// Adding an empty Section to the document...
$section = $phpWord->addSection();
// Adding Text element to the Section having font styled by default...
$section->addText(
    '"Learn from yesterday, live for today, hope for tomorrow. '
        . 'The important thing is not to stop questioning." '
        . '(Albert Einstein)'
);

/*
 * Note: it's possible to customize font style of the Text element you add in three ways:
 * - inline;
 * - using named font style (new font style object will be implicitly created);
 * - using explicitly created font style object.
 */

// Adding Text element with font customized inline...
$section->addText(
    '"Great achievement is usually born of great sacrifice, '
        . 'and is never the result of selfishness." '
        . '(Napoleon Hill)',
    array('name' => 'Tahoma', 'size' => 10)
);

// Adding Text element with font customized using named font style...
$fontStyleName = 'oneUserDefinedStyle';
$phpWord->addFontStyle(
    $fontStyleName,
    array('name' => 'Tahoma', 'size' => 10, 'color' => '1B2232', 'bold' => true)
);
$section->addText(
    '"The greatest accomplishment is not in never falling, '
        . 'but in rising again after you fall." '
        . '(Vince Lombardi)',
    $fontStyleName
);

// Adding Text element with font customized using explicitly created font style object...
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(true);
$fontStyle->setName('Tahoma');
$fontStyle->setSize(13);
$myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
$myTextElement->setFontStyle($fontStyle);

// Saving the document as OOXML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('helloWorld.docx');

// Saving the document as ODF file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'ODText');
$objWriter->save('helloWorld.odt');

// Saving the document as HTML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
$objWriter->save('helloWorld.html');

    $filelist = glob("./data/scenarios/*", GLOB_ONLYDIR);
    foreach ($filelist as $key => $value) {
        $filelist[$key] = iconv(mb_detect_encoding(basename($filelist[$key]), mb_detect_order(), true), "UTF-8", basename($filelist[$key]));
    }
    //var_dump($filelist);
    $viewModel = new ViewModel($filelist);
    $viewModel->setVariable('filelist', $filelist);
    
    return $viewModel;
}

public function displayScenarioInputAction()
    {
		//from URL
	$scenarioName = $this->params()->fromQuery('scenarioName');
	if(empty($scenarioName)) {
		//from hidden form element
		$request = $this->getRequest();
        $post = $request->getPost()->toArray();
		$scenarioName = $post['scenario-name'];
	}
	$filelist = glob("./data/scenarios/".$scenarioName."/*");
	//get all variables from all templates
	$allVariables = array();
	foreach ($filelist as $template) {
		$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('./data/templates/'.basename($template));
		$allVariables = array_merge($allVariables,$templateProcessor->getVariables());
	}
	$allVariablesUnique = array_unique($allVariables);
    $form  = new DisplayScenarioInputForm($allVariablesUnique, $scenarioName);
	//form data handling
		$request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            if (!empty($post)) {
				//var_dump($post); die();
				//create new directory
				$newDirectory = $post['scenario-name']."_".date("d-m-Y_H-i-s");
				$oldmask = umask(0);
				$folders = glob("./data/done/*", GLOB_ONLYDIR);
				if(!in_array($post['scenario-name'],$folders)){
					mkdir("data/done/".$post['scenario-name'], 0777);
				}
				mkdir("data/done/".$post['scenario-name']."/".$newDirectory, 0777);
				umask($oldmask);
				foreach ($filelist as $template) {
					$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("./data/scenarios/".$post['scenario-name']."/".basename($template));
					foreach ($post as $key => $variableFromPost) {
						if($key != 'scenario-name'){
							$templateProcessor->setValue($key, $variableFromPost);							
						}
					}
					$templateProcessor->saveAs('./data/done/'.$post['scenario-name'].'/'.$newDirectory.'/'.basename($template));
				}
				
				$this->forward()->dispatch('Application\Controller\Elaborat', [
				'action' => 'createZipForDownload',
				'newDirectory' => $newDirectory,
				'scenario-name' => $post['scenario-name']
				]);
				
            } else {
                throw new Exception('invalid form, please re-fill');
            }
        }


		$viewModel = new ViewModel(array('form' => $form));
		$viewModel->setVariable('allVariablesUnique', $allVariablesUnique);
		$viewModel->setVariable('scenarioName', $scenarioName);
        
        return $viewModel;
    }

public function uploadFormAction()
{
    $form     = new UploadForm('upload-form');
    $tempFile = null;

    $prg = $this->fileprg($form);
    if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
        return $prg; // Return PRG redirect response
    } elseif (is_array($prg)) {
        if ($form->isValid()) {
            $data = $form->getData();
            // Form is valid, save the form!return
        $this->redirect()->toRoute(null,
        array('module'     => 'application',
              'controller'=>'elaborat',
              'action' => 'uploadForm'));
        } else {
            // Form not valid, but file uploads might be valid...
            // Get the temporary file information to show the user in the view
            $fileErrors = $form->get('image-file')->getMessages();
            if (empty($fileErrors)) {
                $tempFile = $form->get('image-file')->getValue();
            }
        }
    }
        //$form->setData($post);
        //if ($form->isValid()) {
        //    $data = $form->getData();
        //    // Form is valid, save the form!
        //    return $this->redirect()->toRoute(null,
        //     array('module'     => 'application',
        //            'controller'=>'elaborat',
        //            'action' => 'uploadForm'));
        //}

    return array(
        'form'     => $form,
        'tempFile' => $tempFile,
    );
}

public function displayScenariosAction(){
    //echo getcwd();
    $filelist = glob("data\scenarios\*", GLOB_ONLYDIR);
    //var_dump($filelist);
    $viewModel = new ViewModel($filelist);
    $viewModel->setVariable('filelist', $filelist);
    
    return $viewModel;
}

public function displayTemplatesAction(){
    //echo getcwd();
    $filelist = glob("data/templates/*");
    //var_dump($filelist);
    $viewModel = new ViewModel($filelist);
    $viewModel->setVariable('filelist', $filelist);
    
    return $viewModel;
}


public function displayCreateScenarioAction(){
    $form     = new CreateScenarioForm('create-scenario-form');
    $tempFile = null;

    $prg = $this->fileprg($form);
    if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
        return $prg; // Return PRG redirect response
    } elseif (is_array($prg)) {
        if ($form->isValid()) {
            $data = $form->getData();
            // Form is valid, save the form!return
		$this->forward()->dispatch('Application\Controller\Elaborat', [
			'action' => 'createScenario',
			'scenarioName' => $data
		]);
    } else {
        // Form not valid, but file uploads might be valid...
        // Get the temporary file information to show the user in the view
        $fileErrors = $form->get('scenario-name')->getMessages();
        if (empty($fileErrors)) {
            $tempFile = $form->get('scenario-name')->getValue();
        }
    }
    }

    return array(
        'form'     => $form,
        'tempFile' => $tempFile,
    );
    }

public function createScenarioAction(){
    $scenarioName = $this->getEvent()->getRouteMatch()->getParam('scenarioName');
    $oldmask = umask(0);
    mkdir("data/scenarios/".$scenarioName['scenario-name'], 0777);
    umask($oldmask);
    return $this->redirect()->toRoute(null,
     array('module'     => 'application',
            'controller'=>'elaborat'));
    
}

public function fetchTeplatesForScenarioAction(){
    $scenarioName = $this->getEvent()->getRouteMatch()->getParam('scenarioName');
    $templates = glob("data/scenarios/".$scenarioName."/*");
	$this->forward()->dispatch('Application\Controller\Elaborat', [
    'action' => 'displayAddRemoveTemplatesToScenario',
    'templates' => $data
]);
	
}

public function displayAddRemoveTemplatesToScenarioAction(){
	
    $scenarioName = $this->params()->fromQuery('scenarioName');
        $form  = new AddRemoveTemplatesToScenarioForm($scenarioName);
		$request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            if (!empty($post)) {
				$this->forward()->dispatch('Application\Controller\Elaborat', [
				'action' => 'addRemoveTemplatesToScenario',
				'templates' => $post['templates'],
				'scenario-name' => $post['scenario-name']
				]);
            } else {
                throw new Exception('invalid form, please re-fill');
            }
        }
        
		$filelist = glob("data/scenarios/".$scenarioName."/*");
		$viewModel = new ViewModel(array('form' => $form));
		$viewModel->setVariable('filelist', $filelist);
		$viewModel->setVariable('scenarioName', $scenarioName);
        
        return $viewModel;
}


public function addRemoveTemplatesToScenarioAction(){
	$templates = $this->getEvent()->getRouteMatch()->getParam('templates');
	$scenario_name = $this->getEvent()->getRouteMatch()->getParam('scenario-name');
	//var_dump($templates);
	//var_dump($scenario_name); die();
	
	//delete all files in folder for this scenario
	$files = glob('data/scenarios/'.$scenario_name.'/*'); // get all file names
	foreach($files as $file){ // iterate files
		if(is_file($file))
			unlink($file); // delete file
	}
	
	//copy all selected templates to folder for scenario
	foreach($templates as $template){ // iterate files
		if(is_file($template))
			copy($template,"data/scenarios/".$scenario_name."/".basename($template)); // copy file
	}
	
	$this->redirect()->toRoute(null,
     array('module'     => 'application',
            'controller'=>'elaborat'));
	
    //return new ViewModel();
}

public function displayEditScenarioAction(){
    $filelist = glob("./data/scenarios/*", GLOB_ONLYDIR);
    foreach ($filelist as $key => $value) {
        $filelist[$key] = iconv(mb_detect_encoding(basename($filelist[$key]), mb_detect_order(), true), "UTF-8", basename($filelist[$key]));
    }
    //var_dump($filelist);
    $viewModel = new ViewModel($filelist);
    $viewModel->setVariable('filelist', $filelist);
    
    return $viewModel;
}

public function createZipForDownloadAction(){
	$newDirectory = $this->getEvent()->getRouteMatch()->getParam('newDirectory');
	$scenario_name = $this->getEvent()->getRouteMatch()->getParam('scenario-name');

	$filter = new Compress(array(
    'adapter' => 'Zip',
    'options' => array(
        'archive' => "./data/done/".$scenario_name."/".$newDirectory.".zip"
    ),
	));
	$compressed = $filter->filter("./data/done/".$scenario_name."/".$newDirectory);
		
	$this->redirect()->toRoute(null,
     array('module'     => 'application',
            'controller'=>'elaborat',
			'action'=>'downloadZip'),
    array( 'query' => array(
        'newDirectory' => $newDirectory,
		'scenario_name' => $scenario_name
    )));
}

public function downloadZipAction(){
	$newDirectory = $this->params()->fromQuery('newDirectory',null);
	$scenario_name = $this->params()->fromQuery('scenario_name',null);
    $viewModel = new ViewModel();
    $viewModel->setVariable('newDirectory', $newDirectory);
    $viewModel->setVariable('scenario_name', $scenario_name);
    
    return $viewModel;
}

public function diplaySuccessAction(){
    //$this->forward()->dispatch('Application\Controller\Elaborat');
    return new ViewModel();
}

public function vrstaElaborataInputAction()
    {

    $form  = new VrstaElaborataInputForm();
	//form data handling
		$request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            if (!empty($post)) {
				switch ($post['step_number']) {
					case "1":
						$this->redirect()->toRoute(null,
							array('module'     => 'application',
								'controller'=>'elaborat',
								'action'=>'stepOneDisplay'));
						break;
					case "2":
							$this->redirect()->toRoute(null,
							array('module'     => 'application',
								'controller'=>'elaborat',
								'action'=>'stepTwoDisplay'));
						break;
					case "3":
						$this->redirect()->toRoute(null,
							array('module'     => 'application',
								'controller'=>'elaborat',
								'action'=>'stepThreeDisplay'));
						break;
					case "4":
						$this->redirect()->toRoute(null,
							array('module'     => 'application',
								'controller'=>'elaborat',
								'action'=>'stepFourDisplay'));
						break;
					default:
						var_dump($post);
						die();
				}
            } else {
                throw new Exception('invalid form, please re-fill');
            }
        }


		$viewModel = new ViewModel(array('form' => $form));
        
        return $viewModel;
    }
	
	
public function stepOneDisplayAction(){
    $form  = new VrstaElaborataInputForm();
	$viewModel = new ViewModel(array('form' => $form));       
    return $viewModel;
}
	
public function stepTwoDisplayAction(){
    $form  = new UlazniPodaciInputForm();
    $viewModel = new ViewModel(array('form' => $form));       
    return $viewModel;
}
	
public function stepThreeDisplayAction(){
    //$this->forward()->dispatch('Application\Controller\Elaborat');
    return new ViewModel();
}
	
public function stepFourDisplayAction(){
    $form  = new PrijavniListZaKatastarInputForm();
    $viewModel = new ViewModel(array('form' => $form));       
    return $viewModel;
}


public function elaboratDefinitionDisplayAction()
    {

	
    $form  = new elaboratDefinitionDisplayForm();
	//form data handling
		$request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            if (!empty($post)) {
				die('Forma je submitana!');
				$this->forward()->dispatch('Application\Controller\Elaborat', [
				'action' => 'createZipForDownload',
				'newDirectory' => $newDirectory,
				'scenario-name' => $post['scenario-name']
				]);
				
            } else {
                throw new Exception('invalid form, please re-fill');
            }
        }


		$viewModel = new ViewModel(array('form' => $form));
        
        return $viewModel;
    }

	public function getElaboratTable()
     {
         if (empty($this->elaboratTable)) {
             $sm = $this->getServiceLocator();
             $this->elaboratTable = $sm->get('Application\Model\ElaboratTable');
         }
         return $this->elaboratTable;
     }
	 
	public function dohvatiAction()
     {
         return new ViewModel(array(
             'albums' => $this->getElaboratTable()->fetchAll(),
         ));
     }


    public function elaboratSubmitHandlerAction(){
        //all data about elaborat from post
        $request = $this->getRequest();
        $post = $request->getPost()->toArray();

        if (!empty($post['usernames'])) {
            switch ($post['usernames']) {
                case "elaborat-1":
                    $tipElaborata = 'Diobe ili spajanja katastarskih čestica';
                    break;
                case "elaborat-2":
                    $tipElaborata = 'Provedbe dokumenata ili akata prostornog uređenja';
                    break;
                case "elaborat-3":
                    $tipElaborata = 'Evidentiranja pomorskog ili vodnog dobra';
                    break;
                case "elaborat-4":
                    $tipElaborata = 'Evidentiranja, brisanja ili promjene podataka o zgradama ili drugim građevinama';
                    break;
                case "elaborat-5":
                    $tipElaborata = 'Evidentiranja ili promjene podataka o načinu uporabe katastarskih čestica';
                    break;
                case "elaborat-6":
                    $tipElaborata = 'Evidentiranja stvarnog položaja pojedinačnih već evidentiranih katastarskih čestica';
                    break;
                case "elaborat-7":
                    $tipElaborata = 'Evidentiranja međa uređenih u posebnome postupku';
                    break;
                case "elaborat-8":
                    $tipElaborata = 'Provedbe u zemljišnoj knjizi';
                    break;
                case "elaborat-9":
                    $tipElaborata = 'izmjere postojećeg stanja radi ispravljanja zemljišne knjige';
                    break;
                case "elaborat-10":
                    $tipElaborata = $post['ostalo-ime-elaborata'];
                    break;
                default:
                    var_dump($post);
                    die();
            }
        //poziv worda            
            //create new directory
            $oldmask = umask(0);
            $folders = glob("./data/done/*", GLOB_ONLYDIR);
            if(!in_array($post['elaboratID'],$folders)){
                mkdir("data/done/".$post['elaboratID'], 0777);
            }
            umask($oldmask);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('./data/dokumenti/templates/naslovna/'.'Naslovna_stranica_template.docx');

        //set all template variables to empty
        $allVariables = array();
        $allVariables = array_merge($allVariables,$templateProcessor->getVariables());
        $allVariablesUnique = array_unique($allVariables);
        $allVariablesUniqueFliped = array_flip($allVariablesUnique);
        $variables = array_fill_keys(array_keys($allVariablesUniqueFliped), '');

        //varables mapping
        $variables['TIP_ELABORATA'] = $tipElaborata;
        $variables['DATUM'] = date("d.m.Y");
        $variables['OZNAKA_ELABORATA'] = $post['oznakaElaborata'];
        $variables['KATASTARSKA_O'] = $post['KATASTARSKA_O'];
        if (in_array('sadrzaj-8-1',$post['sastavni-dijelovi'])) {
            $variables['TEHNICKO_IZVJESCE'] = 'Tehničko izvješće';
        }
        if (in_array('sadrzaj-8-2',$post['sastavni-dijelovi'])) {
            $variables['IZVJESCE_O_ZGRADAMA'] = 'Izvješće o zgradama i drugim građevinama';
        }
        if (in_array('sadrzaj-8-3',$post['sastavni-dijelovi'])) {
            $variables['IZVJESCE_O_MEDAMA'] = 'Izvješće o međama i drugim zgradama te o novom razgraničenju';
        }
        if (in_array('sadrzaj-9',$post['sastavni-dijelovi'])) {
            $variables['KOPIJA_PLANA'] = 'Prijavni list za katastar';
        }
        if (in_array('sadrzaj-10',$post['sastavni-dijelovi'])) {
            $variables['PRIJAVNI_LIST'] = 'Kopija katastarskog plana za katastar';
        }
        $imeArray = array();
        foreach($post as $key=>$value){
          if("ime" == substr($key,0,3)){
            $imeArray[] = $value;
          }
        }
        $prezimeArray = array();
        foreach($post as $key=>$value){
          if("prezime" == substr($key,0,7)){
            $prezimeArray[] = $value;
          }
        }
        $adresaArray = array();
        foreach($post as $key=>$value){
          if("adresa" == substr($key,0,6)){
            $adresaArray[] = $value;
          }
        }
        $OIBArray = array();
        foreach($post as $key=>$value){
          if("OIB" == substr($key,0,3)){
            $OIBArray[] = $value;
          }
        }

        $brojNarucitelja = count($imeArray);
        $NARUCITELJI = '';
        for ($i=0; $i < $brojNarucitelja; $i++) {
            if (!empty($imeArray[$i])) {
                $NARUCITELJI = $NARUCITELJI.$imeArray[$i].' ';
            }
            if (!empty($prezimeArray[$i])) {
                $NARUCITELJI = $NARUCITELJI.$prezimeArray[$i].', ';
            }
            if (!empty($adresaArray[$i])) {
                $NARUCITELJI = $NARUCITELJI.$adresaArray[$i].', ';
            }
            if (!empty($OIBArray[$i])) {
                $NARUCITELJI = $NARUCITELJI.$OIBArray[$i].',';
            }
            // replace last occ of ',' with '<w:br/>'
            $pos = strrpos($NARUCITELJI, ',');
            if($pos !== false)
            {
                $NARUCITELJI = substr_replace($NARUCITELJI, '<w:br/>', $pos, strlen(','));
            }
        }
        $variables['NARUCITELJI'] = $NARUCITELJI;

        $cesticeArray = array();
        foreach($post as $key=>$value){
          if("brojKatCes" == substr($key,0,10)){
            $cesticeArray[] = $value;
          }
        }
        $brojCestica = count($cesticeArray);
        $KATASTARSKA_C = '';
        for ($i=0; $i < $brojCestica; $i++) {
            if (!empty($cesticeArray[$i])) {
                $KATASTARSKA_C = $KATASTARSKA_C.$cesticeArray[$i].', ';
            }
        }
        // replace last occ of ', ' with ', '
        $pos = strrpos($KATASTARSKA_C, ', ');
        if($pos !== false)
        {
            $KATASTARSKA_C = substr_replace($KATASTARSKA_C, ' ', $pos, strlen(', '));
        }
        $variables['KATASTARSKA_C'] = $KATASTARSKA_C;

        //var_dump($NARUCITELJI); die();

        //setting values in template
        $templateProcessor->setValue($allVariablesUnique,$variables);

        //save as new document
        $pathToOutputFile = './data/done/'.$post['elaboratID'].'/'.'Naslovna_stranica.docx';
        $templateProcessor->saveAs($pathToOutputFile);

        //generate zip with all documents
        $filter = new Compress(array(
        'adapter' => 'Zip',
        'options' => array(
            'archive' => "./data/done/".$post['elaboratID'].".zip"
        ),
        ));
        $compressed = $filter->filter("./data/done/".$post['elaboratID']);

        //render view with parameters
        $viewModel = new ViewModel();
        $viewModel->setVariable('zip', $post['elaboratID'].".zip");
        return $viewModel;

        }
    }

    public function downloadAction() {
        $fileName = $this->params()->fromQuery('zip');
        $name = $fileName;

        $response = new \Zend\Http\Response\Stream();
        
        // Opens the string as a Stream
        $stream = fopen('./data/done/'.$name,'r');
        
        // Get statistics about the stream, extract size
        $stats  = fstat($stream);
        
        $response->setStream($stream);
        $response->setStatusCode(200);
        $response->setStreamName($name);
        $headers = new \Zend\Http\Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="'.$name.'"',
            'Content-Type' => 'application/octet-stream',
            'Content-Length' => $stats['size'],
            'Expires' => '@0', // @0, because zf2 parses date as string to \DateTime() object
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'public'
        ));
        $response->setHeaders($headers);
    return $response;
    }


	
}
