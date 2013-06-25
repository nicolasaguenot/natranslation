<?php
/**
*  @desc 		class NATranslateModuleView		
*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
*	@copyright	2013
*	@version 	 0.1 Beta
*/ 
class NATranslateModuleView {
	
	public static function commonBeforeView($Params, $parseFileParams) {

		$parseFile = new NATranslateParseFile($parseFileParams);
		$dataList = $parseFile->getListToShow();	
		$dataValues = $parseFile->getDataValues();	
		
		// get all context
		$contextList = $parseFile->getAllContext();
		
		$viewParameters = array( 'offset' => 0 );

		$userParameters = $Params['UserParameters'];
		$viewParameters = array_merge( $viewParameters, $userParameters );
	
	
		// return the view
		$tpl = eZTemplate::factory();
		$tpl->setVariable('dataList', $dataList);
		$tpl->setVariable('dataValues', $dataValues);
		$tpl->setVariable('languageList', $parseFile->languageList);
		$tpl->setVariable('limit', $parseFileParams['limit']);
		$tpl->setVariable('offset', $parseFileParams['offset']);
		$tpl->setVariable('sourceKey', $parseFileParams['sourceKey']);
		$tpl->setVariable('numberTotal', $parseFile->getNumberTranslation());
		$tpl->setVariable('contextList', $contextList);
		$tpl->setVariable('view_parameters', $viewParameters);
	
		return $tpl;		
	}
	
	/**
	*	@desc		Return the view to the module
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@param		string $view => the template you want  
	*				mixed $tpl => ezTemplate class loaded
	*	@return		array
	*	@copyright	2013
	*	@version 	 0.1 Beta
	*/	



	public static function getView($view, $tpl=false) {
		if (!$tpl) {
			$tpl = eZTemplate::factory();
		}
		$Result = array();
		$Result['content'] = $tpl->fetch( 'design:translate/'.$view.'.tpl' ); 
		$Result['left_menu'] = "design:translate/leftmenu.tpl"; 
 
		$Result['path'] = array( array( 
			'url' => 'translate/'.$view,
    		'text' => 'NaTranslation' 
		));
		return $Result;
		
	}
	
	/**
	*	@desc		The view : list
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@param		array $Params => view parameter array 
	*	@return		array
	*	@copyright	2013
	*	@version 	 0.1 Beta
	*/	
	public static function translationList($Params) {
		// get the list of translation file
		$fileTranslationList = self::getTranslationListFile();
		if(isset($Params['UserParameters']['sourceKey'])){
			$Params['UserParameters']['sourceKey'] 	=	$Params['UserParameters']['sourceKey'];
		}elseif ( isset($_GET['sourceKey']) && $_GET['sourceKey'] != '') {
			$Params['UserParameters']['sourceKey'] 	=  $_GET['sourceKey'];
		}else{
			$Params['UserParameters']['sourceKey'] 	=  "";
		}	


		// parse file
		$parseFileParams = array(
			'fileTranslationList'	=> $fileTranslationList,
			'limit'					=> isset($Params['UserParameters']['limit']) ? $Params['UserParameters']['limit'] : eZINI::instance('natranslate.ini')->variable( 'NumberPerPage', 'default'), 
			'offset'				=> isset($Params['UserParameters']['offset']) ? $Params['UserParameters']['offset'] : '0',
			'sourceKey'				=> $Params['UserParameters']['sourceKey'],
		);
		
		try {
			$tpl = self::commonBeforeView($Params, $parseFileParams);
			$Result = self::getView('list', $tpl);
			
			return $Result;
		} catch (Exception $e) {
			eZLog::write($e, 'natranslate.log');
		}
	} 
	
	
	/**
	*	@desc		The view : search
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@param		array $Params => view parameter array 
	*	@return		array
	*	@copyright	2013
	*	@version 	 0.1 Beta
	*/	
	public static function translationSearch($Params) {
		// get the list of translation file
		$fileTranslationList = self::getTranslationListFile();
		
		$Params['UserParameters']['sourceKey'] 	=	(isset($Params['UserParameters']['sourceKey']) ? $Params['UserParameters']['sourceKey'] : (isset($_GET['sourceKey']) && $_GET['sourceKey'] != '' ? $_GET['sourceKey'] : ''));
		$Params['UserParameters']['locale']		=	(isset($Params['UserParameters']['locale']) ? $Params['UserParameters']['locale'] : (isset($_GET['locale']) && $_GET['locale'] != '' ? $_GET['locale'] : false));
		
		// parse file
		$parseFileParams = array(
			'fileTranslationList'	=> $fileTranslationList,
			'limit'					=> isset($Params['UserParameters']['limit']) ? $Params['UserParameters']['limit'] : eZINI::instance('natranslate.ini')->variable( 'NumberPerPage', 'default'), 
			'offset'				=> isset($Params['UserParameters']['offset']) ? $Params['UserParameters']['offset'] : '0',
			'sourceKey'				=> $Params['UserParameters']['sourceKey'],
			'dataKey'				=> isset($_GET['dataKey']) && $_GET['dataKey'] != '' ? $_GET['dataKey'] : '',
		);
		
		try {
			$tpl = self::commonBeforeView($Params, $parseFileParams);
			$tpl->setVariable('locale', $Params['UserParameters']['locale']);
			$Result = self::getView('search', $tpl);
			
			return $Result;
		} catch (Exception $e) {
			eZLog::write($e, 'natranslate.log');
		}
	} 
	
	

	
	public function notice($Params) {

		$tpl = eZTemplate::factory();

		$Result = self::getView('notice', $tpl);

		return $Result;

	}
	/**
	*	@desc		The view : edit
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@param		array $Params => view parameter array 
	*	@return		array
	*	@copyright	2013
	*	@version 	 0.1 Beta
	*/	
	public function editTranslation($Params) {
		// get the list of translation file
		$fileTranslationList = self::getTranslationListFile();	
		
		if (isset($_POST['todo']) && $_POST['todo'] == 'validEdit') {
			$params = array();
			unset($_POST['todo']);
			foreach ($_POST as $key => $value) {
				$params[$key] = $value;	
			}	
			$params['fileTranslationList'] = $fileTranslationList;

			try {
				$parseFile = new NATranslateParseFile($params);
				$parseFile->setTranslation();

				eZCache::clearAll();

				eZHTTPTool::redirect($_POST['redirectURI']);
			} catch (Exception $e) {
				eZLog::write($e, 'natranslate.log');
			}
		} else {
			// parse file
		
			$parseFileParams = array(
				'fileTranslationList'	=> $fileTranslationList,
				'sourceKey'				=> isset($Params['UserParameters']['sourceKey']) ? $Params['UserParameters']['sourceKey'] : '',
				'dataKey'				=> isset($Params['UserParameters']['dataKey']) ? $Params['UserParameters']['dataKey'] : '',
				'context'				=> isset($Params['UserParameters']['context']) ? $Params['UserParameters']['context'] : '',
				'file'					=> isset($Params['UserParameters']['file']) ? $Params['UserParameters']['file'] : '',
				
			);
			
			
			try {
				$parseFile = new NATranslateParseFile($parseFileParams);
				$dataforEdit = $parseFile->getTranslationForEdit();	
				
				
				// return the view
				$tpl = eZTemplate::factory();
				$tpl->setVariable('dataforEdit', $dataforEdit);
				$tpl->setVariable('sourceKey', $parseFileParams['sourceKey']);
				$tpl->setVariable('dataKey', $parseFileParams['dataKey']);
				$tpl->setVariable('context', $parseFileParams['context']);
				$tpl->setVariable('file', $parseFileParams['file']);
				$tpl->setVariable('languageList', $parseFile->languageList);


				$Result = self::getView('edit', $tpl);
				
				return $Result;
			} catch (Exception $e) {
				eZLog::write($e, 'natranslate.log');
			}
		}
	}
	
	/**
	*	@desc		The view : generation
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@param		array $params => view parameter array 
	*	@return		array
	*	@copyright	2013
	*	@version 	 0.1 Beta
	*/	
	public static function generateTranslation($Params) {


		$tpl = eZTemplate::factory();
		
		if (isset($_POST['todo']) && $_POST['todo'] == 'chooseExtension') {
			try {
				$tabFileDir = array();
				$tfGene = new NATranslateTranslationFileGenerator();
				foreach ($_POST['extension'] as $extension) {
				    $tabFileDir = array_merge($tabFileDir, $tfGene->scanDirectory(eZExtension::baseDirectory().'/'.$extension));
				    $tfGene->tabFile = array_merge($tfGene->tabFile, $tabFileDir);			    			     	
				}
				$tfGene->analyseFiles();			    
			    $isGenerate = $tfGene->generateXML();
				
				$tpl->setVariable('generation', ($isGenerate ? true : false));
			} catch (Exception $e) {
				eZLog::write($e, 'natranslate.log');
			}
			
		} else {
			$tpl->setVariable('extensionList', eZExtension::activeExtensions());
		}
		// return the view
		$Result = self::getView('generation', $tpl);
		return $Result;
	}
	
	/**
	*	@desc		The view : generation
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@param		array $params => view parameter array 
	*	@return		array
	*	@copyright	2013
	*	@version 	 0.1 Beta
	*/	
	public static function generateFiles($Params) {

		$tpl = eZTemplate::factory();
		
		$extensionIni = eZINI::instance('natranslate.ini');

		$directoryMainExtension = $extensionIni->variable( 'MainExtension', 'directory');
		$directoryExtensions = $extensionIni->variable( 'MainExtension', 'IncludedExtension');
		
		$MainTranslation = $extensionIni->variable( 'MainExtension', 'MainTranslation');
		$MainDirectory = $extensionIni->variable( 'MainExtension', 'MainDirectory');


		$rootExtensionDirectory = eZExtension::baseDirectory();

		$fileTranslationList = array();

		$siteIni = eZINI::instance('site.ini');
		$languagesAllowedToTranslation =  $siteIni->variable( 'RegionalSettings', 'SiteLanguageList');



		if (isset($_POST['todo']) && $_POST['todo'] == 'chooseExtension') {

				$tabFileDir = array();
				$tfGene = new NATranslateTranslationFileGenerator();

				foreach ($_POST['extension'] as $extension) {
					if( $extension != "main" ){

					 	$isGenerate = $tfGene->generateTranslationFolder(eZExtension::baseDirectory().'/'.$extension);

				    }else{

						$isGenerate = $tfGene->generateTranslationFolder($MainDirectory);
						print_r($isGenerate);
				    } 			     	
				}

				$tpl->setVariable('generation', ($isGenerate ? true : false));
			
		
			
		} else {

			$extensionList = array( );

			// IF share/translations file is loaded
	    	if( $MainTranslation == "true" ){
	    		$extensionList[] = "main";
	    	}
	    	// Translations extension
			foreach ($directoryExtensions as $key => $dir) {
				$extensionList[] = $dir;
			}

			$tpl->setVariable('extensionList', $extensionList);

		}
		// return the view
		$Result = self::getView('generate', $tpl);
		
		return $Result;
	}
	
	
	/**
	*	@desc		Get the file list translation
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		array
	*	@copyright	2013
	*	@version 	 0.1 Beta
	*/	
	public static function getTranslationListFile() {

		$extensionIni = eZINI::instance('natranslate.ini');

		$directoryMainExtension = $extensionIni->variable( 'MainExtension', 'directory');
		$directoryExtensions = $extensionIni->variable( 'MainExtension', 'IncludedExtension');
		
		$MainTranslation = $extensionIni->variable( 'MainExtension', 'MainTranslation');
		$MainDirectory = $extensionIni->variable( 'MainExtension', 'MainDirectory');

		$rootExtensionDirectory = eZExtension::baseDirectory();

		$fileTranslationList = array();
		$siteIni = eZINI::instance('site.ini');
		$languagesAllowedToTranslation =  $siteIni->variable( 'RegionalSettings', 'SiteLanguageList');

		// IF share/translations file is loaded
    	if($MainTranslation == "true"){

    		$Directory = $MainDirectory.'/translations'; 

    		$dirMainTranslationList = eZDir::findSubitems($Directory, false, true);

			foreach ($dirMainTranslationList as $dirMain) {
				$locale = substr($dirMain, (strripos($dirMain, '/') +1));

			
				$fileListMain = eZDir::findSubitems($dirMain, false, true);
			

				foreach ($fileListMain as $fileMain) {
					if( in_array($locale, $languagesAllowedToTranslation) && substr($fileMain, -2) == "ts" ){
						$fileTranslationList[$locale][] = $fileMain;
					}
				}
			}
    	}

    	// Translations extension
		foreach ($directoryExtensions as $key => $dir) {
			$baseDirectory = $rootExtensionDirectory.'/'.$dir.'/translations'; 

			$dirTranslationList = eZDir::findSubitems($baseDirectory, false, true);

			foreach ($dirTranslationList as $dir) {
				$locale = substr($dir, (strripos($dir, '/') +1));

				$fileList = eZDir::findSubitems($dir, false, true);

				foreach ($fileList as $file) {
					
					if( in_array($locale, $languagesAllowedToTranslation) && substr($file, -2) == "ts" ){

						$fileTranslationList[$locale][] = $file;

					}
				}
			}
		}

		return $fileTranslationList;
	}
}

?>
