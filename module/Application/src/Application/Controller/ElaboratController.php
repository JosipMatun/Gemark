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
						echo "";
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
    //$this->forward()->dispatch('Application\Controller\Elaborat');
    return new ViewModel();
}
	
public function stepThreeDisplayAction(){
    //$this->forward()->dispatch('Application\Controller\Elaborat');
    return new ViewModel();
}
	
public function stepFourDisplayAction(){
    //$this->forward()->dispatch('Application\Controller\Elaborat');
    return new ViewModel();
}


public function elaboratDefinitionDisplayAction()
    {

	//generate elaboratID and store it to session
	$t = microtime(true);
	$micro = sprintf("%06d",($t - floor($t)) * 1000000);
	$d = new \DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
	$elaboratID = $d->format("YmdHisu"); // note at point on "u"

	$session = new Container('base');
	$session->offsetSet('elaboratID', $elaboratID);	
	
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
	
}
