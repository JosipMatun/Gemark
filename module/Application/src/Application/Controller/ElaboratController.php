<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Application\form\UploadForm;
use Application\form\CreateScenarioForm;
use Zend\Http\PhpEnvironment\Request;
use Zend\Filter\File\RenameUpload;

class ElaboratController extends AbstractActionController
{
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

    $filelist = glob("data\scenarios\*", GLOB_ONLYDIR);
    foreach ($filelist as $key => $value) {
        $filelist[$key] = iconv(mb_detect_encoding(basename($filelist[$key]), mb_detect_order(), true), "UTF-8", basename($filelist[$key]));
    }
    //var_dump($filelist);
    $viewModel = new ViewModel($filelist);
    $viewModel->setVariable('filelist', $filelist);
    
    return $viewModel;
}

        public function templateAction()
    {        

// Template processor instance creation
echo date('H:i:s') , ' Creating new TemplateProcessor instance...';
$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('data\templates\firstTemplate.docx');

$templateProcessor->setValue('CLONEME', 'upisana vrijednost');

$varsFromTemplate = $templateProcessor->getVariables();
var_dump($varsFromTemplate);

echo date('H:i:s'), ' Saving the result document...';
$templateProcessor->saveAs('data\done\firstTemplate_replaced.docx');
//var_dump($templateProcessor);


    	return new ViewModel();
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
                    'controller'=>'elaborat',
                    'action' => 'displaySuccess'));
            
}

public function diplayAddTempltesToScenarioAction(){

}


public function diplaySuccessAction(){
    //$this->forward()->dispatch('Application\Controller\Elaborat');
    return new ViewModel();
}

}
