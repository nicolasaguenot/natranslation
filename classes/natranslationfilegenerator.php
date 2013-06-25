<?php
/**
*  @desc 		class NATranslateTranslationFileGenerator		
*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
*	@copyright	2012
*	@version 	 0.1 Beta
*/
class NATranslateTranslationFileGenerator {
    
    public $tabPath;
    public $tabFile;
    public $languageList;
    public $extensionIni;
    private $tabKey;
    
    /**
	*	@desc		Constructeur
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		void
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/	
    public function __construct() {
        try {
            $this->tabPath = array();
            $this->tabFile = array();
            $this->tabKey = array();

            $this->extensionIni = eZINI::instance('natranslate.ini');
        } catch (Exception $e) {
            echo $e;
        }
    }
    
    /**
	*	@desc		Add path to the list  
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@params		string	$path => path for check files
	*	@return		void
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
    public function addPath($path = null) {
        if ($path === null) {
            throw new Exception('A path can not be null !');
        } else {
            if (is_string($path)) {
                $this->tabPath[] = $path;
            }
        }
    }
    
    /**
	*	@desc		Add file to the list 
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@params		string	$file => file for checing
	*	@return		void
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
    public function addFile($file = null) {
        if ($file === null) {
            throw new Exception('A file name can not be null !');
        } else {
            if (is_string($file)) {
                $this->tabFile[] = $file;
            }
        }
    }
    
    /**
	*	@desc		Get the list of path
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		array
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
    public function getTabPath() {
        $listPath = '';
        $i = 1;
        foreach ($this->tabPath as $path) {
            $listPath .= $path . ($i != sizeof($this->tabPath) ? "\n" : '');
            $i++;
        }
        return $listPath;   
    }
    
    /**
	*	@desc		Get the list of file
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		array
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
    public function getTabFile() {
        $listFile = '';
        $i = 1;
        foreach ($this->tabFile as $file) {
            $listFile .= $file . ($i != sizeof($this->tabFile) ? "\n" : '');
            $i++;
        }
        return $listFile;
    }
    
    /**
	*	@desc		Analyse all files to find translation
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		array
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
    public function analyseFiles() {
        $finalMatches = array();
        $tabTrad = array();
        foreach($this->tabFile as $file) {
            $matches = array();
            $fp = fopen($file,"r");
            while(!feof($fp)) {
                $buffer = fgets($fp);
                if (preg_match_all('#\{[\'"]([^\}]+)[\'"]\|i18n\([\'"]([^}]+)[\'"]\)\}#', $buffer, $tmpMatches)) {
                    $matches['template'][] = $tmpMatches;
                } else {
                	if (preg_match_all('#ezpI18n::tr\([ ]*[\'"]([^\)\}]+)[\'"][ ]*,[ ]*[\'"]([^\)\}]+)[\'"][ ]*\)#', $buffer, $tmpMatches)) {
	                	$matches['php'][] = $tmpMatches;
                	}
                }
            }
            fclose($fp);
            $finalMatches = array_merge($finalMatches, $matches);            
            foreach($finalMatches as $fileType => $matchList) {
            	foreach ($matchList as $match) {					            	
	                $tradKeys = ($fileType == 'template'  ? $match[2] : $match[1]);
	                $tradValues = ($fileType == 'template'  ? $match[1] : $match[2]);
	                foreach ($tradKeys as $key => $value) {
	                    if (!isset($tabTrad[$value])) {	                    	
	                        $tabTrad[$value] = array();
	                    }
	                }
	                foreach ($tradKeys as $key => $value) {
	                    if (!in_array($tradValues[$key], $tabTrad[$value])) {
	                        $tabTrad[$value][] = $tradValues[$key];
	                    }
	                }
            	}
            }
        }
        $this->tabKey = $tabTrad;
        return $tabTrad;
    }
    
   /**
    *   @desc       Generate xml file for all locale on your site with all translation found
    *   @author     Nicolas AGUENOT <contact@nicolasaguenot.com>
    *   @return     bool
    *   @copyright  2012
    *   @version     0.1 Beta
    */
    public function generateTranslationFolder($path) {
     
        $siteIni = eZINI::instance('site.ini');
        $languagesAllowedToTranslation =  $siteIni->variable( 'RegionalSettings', 'SiteLanguageList');
        $defaultPath = "";
        $newpath = "";

        $defaultLocale = $this->extensionIni->variable( 'MainLocale', 'locale');

        $error = false;
        foreach ( $languagesAllowedToTranslation as $showLanguage ){
       
            if( $showLanguage == $defaultLocale ){
                 $defaultPath = $path . "/translations/" . $showLanguage;
            }

            if( opendir( $path . "/translations/" . $showLanguage) ){
               
                $newpath = $path. "/translations/" . $showLanguage;

            }else{

                $newpath = $path. "/translations/" . $showLanguage;
                eZDir::mkdir($newpath, 0755);
            }
            if(!$this->generateTranslationFile( $newpath , $defaultPath )){
                $error = true;
            }
        } 

        return $error;
    }

    public function generateTranslationFile($path, $defaultPath) {
        
       
        if( file_exists( $path . "/translation.ts" ) ){

            if($this->extensionIni->variable( 'Params', 'ArchiveOldTranslations')){
                $this->archiveTranslationFile($path);
            }
            
            $this->checkAllTranslations( $path . "/translation.ts", $defaultPath . "/translation.ts" );

        }else{
            if ( @copy( $defaultPath . "/translation.ts" , $path . "/translation.ts") ){
                
                $this->checkAllTranslations( $path . "/translation.ts", $defaultPath . "/translation.ts" );

            }

        }  
        
    }
    public function archiveTranslationFile($path){


        $archiveFolderName = $this->extensionIni->variable( 'Params', 'ArchiveFolderName');
        chmod($path, 0755);

        mkdir($path. "/" .$archiveFolderName, 0755, true);
        
        copy( $path . "/translation.ts" , $path . "/" . $archiveFolderName . "/translation@override.".date('Ymd-His').".ts");
        

    }
    public function checkAllTranslations($file, $defaultFile){

        $defaultTsFile = new DOMDocument();
        $defaultTsFile->load($defaultFile);

        $defaultTs = simplexml_import_dom($defaultTsFile);

        $tsFile = new DOMDocument();
        $tsFile->load($file);


        $ts = simplexml_import_dom($tsFile);
        
  
        
        $sourceName = "";
        foreach ($defaultTs as $key => $Tabvalue) {
            $sourceName = $Tabvalue->name[0];
            foreach ($Tabvalue->message as $element) {
                $query = "//context[name=".(strpos($sourceName, "'") === false ? "'$sourceName'" : "\"$sourceName\"")."]/message[source=".(strpos($element->source, "'") === false ? "'$element->source'" : "\"$element->source\"")."]";
                
                try {
                    if (! $ts->xpath($query) ) {
            
                        $elToInsert = $defaultTs->xpath($query);
                     
                        $message = $tsFile->createElement('message');
                        $source = $tsFile->createElement('source', (string)$elToInsert[0]->source);
                        $translation = $tsFile->createElement('translation');
                      
                        $querySourceName = "//context[name=".(strpos($sourceName, "'") === false ? "'$sourceName'" : "\"$sourceName\"")."]";
                       
                        if (!$ts->xpath($querySourceName) ) {
                            $context = $ts->addChild('context');
                            $name = $context->addChild('name', $sourceName);
                            $message = $context->addChild('message');
                            $source = $message->addChild('source', (string)$elToInsert[0]->source);
                            $translation = $message->addChild('translation');
                            
                        }else{

                            $name = $ts->xpath("//context[name=".(strpos($sourceName, "'") === false ? "'$sourceName'" : "\"$sourceName\"")."]");
                            $context = $ts->xpath("//context[name=".(strpos($sourceName, "'") === false ? "'$sourceName'" : "\"$sourceName\"")."]/..");
                            
                            $message = $name[0]->addChild('message');
                            $source = $message->addChild('source', (string)$elToInsert[0]->source);
                            $translation = $message->addChild('translation');

                            
                        }

                    }
                } catch (Exception $e) {
                    eZLog::write($e, 'natranslate.log');
                }     
              
            }
        }
        $tsFile->save($file, LIBXML_NOEMPTYTAG);


    }
    /**
	*	@desc		Generate xml file for all locale on your site with all translation found
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@return		bool
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
    public function generateXML() {
    	$this->languageList = eZContentLanguage::fetchList();
    	$directoryMainExtension = eZINI::instance('natranslate.ini')->variable( 'MainExtension', 'directory');		
		$baseDirectory = eZExtension::baseDirectory().'/'.$directoryMainExtension.'/translations';
    	$this->createLocaleDirIfNotExist($baseDirectory);
    	
        $localeOverride = eZINI::instance('natranslate.ini')->variable( 'LocaleOverride', 'locale');
        
        // verification file translation exist
        foreach ($this->languageList as $language) {
        	$locale = (array_key_exists($language->Locale, $localeOverride) ? $localeOverride[$language->Locale] : $language->Locale);

        	if (file_exists($baseDirectory.'/'.$locale.'/translation.ts')) {
				$saveXml = $this->addTranslationIfNotExist($baseDirectory.'/'.$locale.'/translation.ts');			
        	} else {        		
		        $saveXml = $this->addTranslationFile($baseDirectory.'/'.$locale.'/translation.ts');
        	}
        } 
        return $saveXml;
    }
    
    /**
	*	@desc		Add the new translation found in the existing file of translation 
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@params		string	$file => the file where the translation is adding
	*	@return		bool
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
    public function addTranslationIfNotExist($file) {

        $tsFile = new DOMDocument();
        $tsFile->load($file);
        
        $xpath = new DOMXpath($tsFile);
        $ts = $tsFile->documentElement;
       
        foreach($this->tabKey as $sourceName => $tabElement) {    

            foreach ($tabElement as $element) {
            	$query = "//context[name=".(strpos($sourceName, "'") === false ? "'$sourceName'" : "\"$sourceName\"")."]/message[source=".(strpos($element, "'") === false ? "'$element'" : "\"$element\"")."]";
            	try {
            		if ($xpath->query($query) && !$xpath->query($query)->item(0)) {
            		
	            		$message = $tsFile->createElement('message');
	                	$source = $tsFile->createElement('source', $element);
	                	$translation = $tsFile->createElement('translation');
	            		
	            		$querySourceName = "//context[name=".(strpos($sourceName, "'") === false ? "'$sourceName'" : "\"$sourceName\"")."]";
	            		if (!$xpath->query($querySourceName)->item(0)) {
	            			$context = $tsFile->createElement('context');
	            			$name = $tsFile->createELement('name', $sourceName);
	            			
	            			$context->appendChild($name);
		                	$message->appendChild($source);
		                	$message->appendChild($translation);
		                	$context->appendChild($message);
		                	$ts->appendChild($context);
		                	
	            		} else {
	            			$name = $xpath->query("//context[name=".(strpos($sourceName, "'") === false ? "'$sourceName'" : "\"$sourceName\"")."]")->item(0);
	            			$context = $xpath->query("//context[name=".(strpos($sourceName, "'") === false ? "'$sourceName'" : "\"$sourceName\"")."]/..")->item(0);
		                	
		                	$message->appendChild($source);
		                	$message->appendChild($translation);
		                	$name->appendChild($message);
	            		}
            		}
        		} catch (Exception $e) {
					eZLog::write($e, 'natranslate.log');
				}            	
            }
        }   
        
        try {
        	if ($unlinkFile = unlink($file)) {
        		$saveXml = $tsFile->save($file, LIBXML_NOEMPTYTAG);
        	}
        } catch (exception $e) {
        	echo $e;
        }
       	return $saveXml;
    }
    
    /**
	*	@desc		Create translation file with all translation found
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@params		string	$file => the file where the translation is adding
	*	@return		bool
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
    public function addTranslationFile($file) {
    	
		$doctype = DOMImplementation::createDocumentType("TS"); 
        $tsFile = DOMImplementation::createDocument(null, 'TS', $doctype);
        $tsFile->encoding = 'UTF-8';
        $tsFile->formatOutput = true;
    	
    	$ts = $tsFile->documentElement;
    	foreach($this->tabKey as $sourceName => $tabElement) {          
    		$context = $tsFile->createElement('context');  
            $name = $tsFile->createELement('name', $sourceName);            
            $context->appendChild($name);
            foreach ($tabElement as $element) {
                $message = $tsFile->createElement('message');
                $source = $tsFile->createElement('source', $element);
                $translation = $tsFile->createElement('translation');
                
                $message->appendChild($source);
                $message->appendChild($translation);
                $context->appendChild($message);             
            }
            $ts->appendChild($context);
        }     
        $saveXml = $tsFile->save($file, LIBXML_NOEMPTYTAG);
        return $saveXml;
    }
    
    /**
	*	@desc		Create all local directory for every language of your site
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@params		string	$baseDirectory => the base folder you want to create local dir
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
    public function createLocaleDirIfNotExist($baseDirectory) {
		if (!is_dir($baseDirectory)) {
			eZDir::mkdir($baseDirectory, octdec('0775'));
		}
		$localeOverride = eZINI::instance('natranslate.ini')->variable( 'LocaleOverride', 'locale');
    	foreach ($this->languageList as $language) {
    		$locale = (array_key_exists($language->Locale, $localeOverride) ? $localeOverride[$language->Locale] : $language->Locale);
    		if (!is_dir($baseDirectory.'/'.$locale)) {
    			eZDir::mkdir($baseDirectory.'/'.$locale, octdec('0775'));
    		}
    	}
    }
    
    /**
	*	@desc		Scan directory to find translation
	*	@author 	Nicolas AGUENOT <contact@nicolasaguenot.com>
	*	@params		string	$directory => the base folder you want to scan
	*	@return 	array
	*	@copyright	2012
	*	@version 	 0.1 Beta
	*/
    public function scanDirectory($directory = null) {
        if ($directory === null) {
            throw new Exception('Directory param can not be null');
        }
        
        try {
            $openDirectory = opendir($directory);
            $tabFile = array();
            $tabExclude = array(
                '.',
                '..',
                '.svn',
                'stylesheets',
                'images',
                'javascript',
                'flash',
            );
            while($element = readdir($openDirectory)) {
                $path = $directory .'/'. $element;   
                if (is_dir($path) && !in_array($element, $tabExclude)) {
                    $tabFile = array_merge($tabFile, self::scanDirectory($path));
                } else {
                    if (preg_match('#\.tpl#', $element)) {
                        $tabFile[] = $path;
                    } elseif (preg_match('#^((?!ini).)*.php#', $element)) {
                    	$tabFile[] = $path;
                    }
                }
            }
        } catch (Exception $e) {
            echo $e;
        }
        return $tabFile;
    }
}
?>
