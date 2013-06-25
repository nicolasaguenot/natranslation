<?php
/**
*  @desc 		class NATranslateParseFile		
*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
*	@copyright	2012
*	@version 	 0.1 Beta
*/	
class NATranslateParseFile {
	
	public $fileList = array();	
	public $languageList = array();
	public $xmlList = false;
	public $datas = array();
	public $dataValues = array();
	public $mainLocaleKey = false;
	
	public $numberPerPage = 25;
	public $offset = 0;
	public $compteur = 0;
	public $countMessagePerContext = 0;
	public $numberTotal = 0;
	
	public $currentContext = false;
	public $currentSourceContext = false;
	public $currentNameTranslate = false; 
	public $currentValuesTranslate = array();
	public $futureValuesTranslate = array();
	
	/**
	*	@desc		Constructeur
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@param		array $params => 
	*				contains :  - fileTranslationList (for settings local use in your site)
	*							- limit (number total of pages)
	*							- offset
	*							- sourceKey (key of source context translation's file)
	*							- dataKey (key of source message translation's file)
	*							- translate (future value for source message translation's file)
	*	@return		void
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/	
	public function __construct($params) {
		if (is_array($params) && isset($params['fileTranslationList'])) {
			$this->setFileListById($params['fileTranslationList']);

			$this->numberPerPage = (isset($params['limit']) ? $params['limit'] : eZINI::instance('natranslate.ini')->variable( 'NumberPerPage', 'default'));
			$this->offset = (isset($params['offset']) ? $params['offset'] : $this->offset);

			$this->currentSourceContext = (isset($params['sourceKey']) ? $params['sourceKey'] : $this->currentSourceContext);

			$this->currentContext = (isset($params['context']) ? $params['context'] : $this->currentSourceContext);
			$this->currentFile = (isset($params['file']) ? $params['file'] : $this->currentFile);

			$this->currentNameTranslate = (isset($params['dataKey']) ? $params['dataKey'] : $this->currentNameTranslate);			
			
			$this->futureValuesTranslate = (isset($params['translate']) ? $params['translate'] : $this->futureValuesTranslate);
		
	
		
			// get the main locale key
			$this->mainLocaleKey = $this->getLanguageIdByLocale(eZINI::instance('natranslate.ini')->variable( 'MainLocale', 'locale'));
		} else {
			throw new Exception('Le constructeur doit avoir un tableau en paramétre.');
		}
	} 
	
	/**
	*	@desc		settings local use in your site
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@param		array $fileTranslationList => translation's file list
	*	@return		void
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
	public function setFileListById($fileTranslationList) {
		$languageListe = eZContentLanguage::fetchList();
		$localeOverride = eZINI::instance('natranslate.ini')->variable( 'LocaleOverride', 'locale');
		foreach ($languageListe as $language) {


			$this->languageList[$language->ID] = array(
				'locale' 	=> (array_key_exists($language->Locale, $localeOverride) ? $localeOverride[$language->Locale] : $language->Locale),
				'name'		=> $language->Name,
			); 
		}
		

		foreach ($fileTranslationList as $fileKey => $files) {
			foreach ($this->languageList as $key => $language) {
				if (substr($fileKey, 0,  6) == substr($language['locale'], 0, 6)) {

					foreach ($files as $keyFile => $file) {

						$this->fileList[$key][] = $file;	

					}
				}
			}
		}
					
	}
	
	/**
	*	@desc		Get the translation's list (source and values for all languages) you want to see
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		array 
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
	public function getListToShow() {
		$this->parse();		
		$this->sortTranslationListFile();
		return $this->datas;
	}
	
	/**
	*	@desc		xml parse for all translation's file
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		void
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
	public function parse() {



		foreach ($this->fileList as $keys => $files) {
			foreach ($files as $key => $file) {
			
				if (file_exists($file)) {
					try {
						$this->xmlList[$keys][$file] = simplexml_load_file($file);
	
					} catch (Exception $e) {
						eZLog::write($e, 'natranslate.log');
					}
				}
			}
		}

	}
	
	/**
	*	@desc		Get the  translation source list you want to see
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		void
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
	public function sortTranslationListFile() {	
		$this->datas = array();	
		$this->dataValues = array();
		$this->compteur = 0;
		$this->countMessagePerContext = 0;
	
        if ($this->currentSourceContext) {
        	try {
				$query = "//context[name=".(strpos($this->currentSourceContext, "'") === false ? "'$this->currentSourceContext'" : "\"$this->currentSourceContext\"")."]";
				if ($context = $this->xmlList[$this->mainLocaleKey]->xpath($query)) {
					$context = $context[0];
					$this->getListByContext($context);
				}
			} catch (Exception $e) {
				eZLog::write($e, 'natranslate.log');
			}
        } else {
        	$ContextName = "";
	
  			foreach($this->xmlList[$this->mainLocaleKey] as $nameFile=>$contexts) {

	        
        		foreach ($contexts->context as $context) {
        			if ($this->compteur >= ($this->offset + $this->numberPerPage)) {
						break;	
					}
        			$this->getListByContext($context, $nameFile);
        		}
				
				
			}
        }	
        $this->getListValuesByContext();
	}
	
	/**
	*	@desc		Get list message by source context
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		void
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
	public function getListByContext($context, $nameFile) {
		
		$this->countMessagePerContext += count($context->message);

				
		if ($this->offset < $this->countMessagePerContext) {
			foreach ($context->message as $message) {
				
				if ($this->currentNameTranslate) {
					if (strtolower((string)$message->source) != strtolower($this->currentNameTranslate) && strpos(strtolower((string)$message->source), strtolower($this->currentNameTranslate)) === false) {
						continue;
					}	
				}
				if ($this->compteur >= $this->offset && 
					$this->compteur < ($this->offset + $this->numberPerPage) && 
					substr( (string)$context->name, 0, 12) != "design/admin" && 
					
					substr( (string)$context->name, 0, 15) != "design/standard" && 
					substr( (string)$context->name, 0, 6) != "kernel" && 
					!in_array( (string)$context->name , eZINI::instance('natranslate.ini')->variable( 'Context', 'ExcludedContext') ) ) {
				
					
					$this->datas[str_replace('/', '|', $nameFile)  . "--" . (string)$context->name][] = (string)$message->source;
					
				}
			}
		} else {
			$this->compteur = $this->countMessagePerContext;
		}
	
	}
	
	/**
	*	@desc		Get the translation values corresponds to the  source list you want to see
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		void
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
	public function getListValuesByContext() {
		// search
		foreach($this->xmlList as $localeKey => $xml) {
			if ($this->currentNameTranslate) {
				try {
					$query = "//context/message/source/text()[contains(translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), ".strtolower((strpos($this->currentNameTranslate, "'") === false ? "'$this->currentNameTranslate'" : "\"$this->currentNameTranslate\"")).")]/../..";
					if ($elements = $xml->xpath($query)) {
						foreach ($elements as $element) {

							$this->dataValues[$localeKey][(string)$element->source] = (string)$element->translation;
						} 
					}
				} catch (Exception $e) {
					eZLog::write($e, 'natranslate.log');
				}
			} else {
				// list
				
				
					foreach ($messageList as $message) {
						try {
							$query = "//context[name=".(strpos($sourceKey, "'") === false ? "'$sourceKey'" : "\"$sourceKey\"")."]/message[source=".(strpos($message, "'") === false ? "'$message'" : "\"$message\"")."]/translation";
							$element = $xml->xpath($query);
				
							$this->dataValues[$localeKey][$message] = (string)$element[0];
						} catch (Exception $e) {
							eZLog::write($e, 'natranslate.log');
						}
					}
			}
		}

	}
	
	/**
	*	@desc		Get the total number of translation
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		int
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
	public function getNumberTranslation() {


		if ($this->numberTotal == 0) {

	
			foreach ($this->datas as $dataArray) {

				$this->numberTotal += count($dataArray);	
			
			}
			
		}

		return $this->numberTotal;
	}
	
	/**
	*	@desc		Get all the translation for one source message for edit
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		array
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
	public function getTranslationForEdit() {
		$this->parse();
	
		$source = implode( "%", explode( "[param]", $this->currentNameTranslate) ) ;
		$query = "//context[name=".(strpos($this->currentContext, "'") === false ? "'$this->currentContext'" : "\"$this->currentContext\"")."]/message[source=".(strpos($source, "'") === false ? "'$source'" : "\"$source\"")."]/translation";
		
		
		$limitCutBeginFile = 15;

		$beginFile = substr($this->currentFile, 0, $limitCutBeginFile);

	
		foreach($this->xmlList as $keyXml => $xml) {

			foreach ($xml as $key => $xmlfile){
				
				if ($beginFile == substr($key, 0, $limitCutBeginFile)){
				
					if( $xmlfile->xpath($query) ){
						try {

								$element = $xmlfile->xpath($query);;
								$this->currentValuesTranslate[$keyXml] = (string)$element[0];
						} catch (Exception $e) {
							eZLog::write($e, 'natranslate.log');
						}
					}
				
				}
			}	

		}
		return $this->currentValuesTranslate;
	}	
	
	
	public function checkTranslationLanguage() {
		$this->parse();
	
		$source = implode( "%", explode( "[param]", $this->currentNameTranslate) ) ;
		$query = "//context[name=".(strpos($this->currentContext, "'") === false ? "'$this->currentContext'" : "\"$this->currentContext\"")."]/message[source=".(strpos($source, "'") === false ? "'$source'" : "\"$source\"")."]/translation";
		$limitCutBeginFile = 15;
		$beginFile = substr($this->currentFile, 0, $limitCutBeginFile);
		$languageTranslated = array();
		$thislanguage = 0;
		
		foreach($this->xmlList as $keyXml => $xml) {
			$thislanguage = 0;
			foreach ($xml as $key => $xmlfile){
				if ($beginFile == substr($key, 0, $limitCutBeginFile)){
					if( $xmlfile->xpath($query) ){
						try {
							$element = $xmlfile->xpath($query);
								
							if(strlen($element[0]) > 0){
								$lang = eZContentLanguage::fetch ( $keyXml );
								if($lang->Locale != "eng-GB" && (string)$element[0] != $source){
									$languageTranslated[] = $keyXml;
								}else{
							
									if($lang->Locale == "eng-GB"){
										$languageTranslated[] = $keyXml;
									}
								}
							}
						} catch (Exception $e) {
							eZLog::write($e, 'natranslate.log');
						}
					}
				}
			}	
		}
		return $languageTranslated;
	}	
	
	public function checkTranslation() {
		$this->parse();
	
		$source = implode( "%", explode( "[param]", $this->currentNameTranslate) ) ;
		$query = "//context[name=".(strpos($this->currentContext, "'") === false ? "'$this->currentContext'" : "\"$this->currentContext\"")."]/message[source=".(strpos($source, "'") === false ? "'$source'" : "\"$source\"")."]/translation";
		$limitCutBeginFile = 15;
		$beginFile = substr($this->currentFile, 0, $limitCutBeginFile);
		$languageTranslated = array();
		$thislanguage = 0;
		
		foreach($this->xmlList as $keyXml => $xml) {
			$thislanguage = 0;
			foreach ($xml as $key => $xmlfile){
				if ($beginFile == substr($key, 0, $limitCutBeginFile)){
					if( $xmlfile->xpath($query) ){
						try {
							$element = $xmlfile->xpath($query);
								
							if(strlen($element[0]) > 0){
								$lang = eZContentLanguage::fetch ( $keyXml );
								if($lang->Locale != "eng-GB"){
									$languageTranslated[] = $element[0];
								}
							}
						} catch (Exception $e) {
							eZLog::write($e, 'natranslate.log');
						}
					}
				}
			}	
		}
		return $languageTranslated;
	}		
	
	/**
	*	@desc		Set the translation value for one or all language found for message source
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		bool
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
	public function setTranslation() {
		$returnValue = false;
		$this->parse();

		$limitCutBeginFile = 15;

		$beginFile = substr($this->currentFile, 0, $limitCutBeginFile);

		

		$source = implode( "%", explode( "[param]", $this->currentNameTranslate) ) ;

		$query = "//context[name=".(strpos($this->currentContext, "'") === false ? "'$this->currentContext'" : "\"$this->currentContext\"")."]/message[source=".(strpos($source, "'") === false ? "'$source'" : "\"$source\"")."]/translation";

		foreach($this->xmlList as $keyXml => $xml) {


			if (isset($this->futureValuesTranslate[$keyXml])) {

				foreach ($xml as $key => $xmlfile){
		
					
					if ($beginFile == substr($key, 0, $limitCutBeginFile)){
						try {
							
							if( $xmlfile->xpath($query) ){
								
								$element = $xmlfile->xpath($query);
								$newValue = $this->futureValuesTranslate[$keyXml];
								
								$element[0][0] = $newValue;
								if (file_exists($key)) {
									$returnValue = true;
									$fp = fopen($key, 'w');
									fwrite($fp, $xmlfile->asXML());
									fclose($fp);
								}
							}

						} catch (Exception $e) {
							eZLog::write($e, 'natranslate.log');
						}
						
					
					}
				}	


			}
		}
	
		return $returnValue;
	}
	

	/**
	*	@desc		Get All context
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		array
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
	public function getAllContext() {
		$contextList = array();
				
		foreach($this->xmlList[$this->mainLocaleKey] as $context) {
			if (!in_array((string)$context->name, $contextList)) {
				$contextList[] = (string)$context->name; 
			}
		}
		sort($contextList);
		return $contextList;
	}
	
	/**
	*	@desc		Get values translation'list 
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		array
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
	public function getDataValues() {

	
		return $this->dataValues;
	}
	
	/**
	*	@desc		Get the language name by an id
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		string
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
	public function getLanguageNameById($id) {

		return $this->languageList[$id]['name'];
	}
	
	/**
	*	@desc		Get the language's id by local
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		int
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
	public function getLanguageIdByLocale($locale) {
	
		$language = eZContentLanguage::fetchByLocale($locale);
		
	
		return $language->ID;
	}
}
	
?>
