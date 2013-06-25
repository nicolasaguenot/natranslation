<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Website Toolbar
// SOFTWARE RELEASE: 1.3.0
// COPYRIGHT NOTICE: Copyright (C) 1999-2010 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

class NaCheckTranslationLanguage
{
    function NaCheckTranslationLanguage()
    {
    }

    function operatorList()
    {
        return array( 'nachecktranslationlanguage' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'nachecktranslationlanguage' => array( 'dataKey' => array( 'type' => 'string',
                                                          'required' => true,
                                                          'default' => '' ),
  												'file' => array( 'type' => 'string',
                                                          'required' => true,
                                                          'default' => '' )	,
													'context' => array( 'type' => 'string',
                                                          'required' => true,
                                                          'default' => '' )	

													) );
    }

    function modify( $tpl, $operatorName, $operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
	
		$fileTranslationList = NATranslateModuleView::getTranslationListFile();
		
		$parseFileParams = array(
				'fileTranslationList'	=> $fileTranslationList,
				'sourceKey'				=> isset($namedParameters['sourceKey']) ? $namedParameters['sourceKey'] : '',
				'dataKey'				=> isset($namedParameters['dataKey']) ? $namedParameters['dataKey'] : '',
				'context'				=> isset($namedParameters['context']) ? $namedParameters['context'] : '',
				'file'					=> isset($namedParameters['file']) ? $namedParameters['file'] : '',
				
			);

		try {
				$parseFile = new NATranslateParseFile($parseFileParams);
				$dataLanguage = $parseFile->checkTranslationLanguage();	
				foreach( $dataLanguage as $key => $idlanguage){
					$lang = eZContentLanguage::fetch ( $idlanguage );
					
					$operatorValue .= "<img style='margin:0px 3px;' src='../../share/icons/flags/".$lang->Locale.".gif' />";
				}
				
		} catch (Exception $e) {
				eZLog::write($e, 'natranslate.log');
			
			}

	
		return $operatorValue;

		
    }
}

?>
