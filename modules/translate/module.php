<?php

$module = array( 'name' => 'translate' );
 
$ViewList = array();
$ViewList['list'] = array( 'script' => 'list.php',
  								'default_navigation_part' => 'translatenavigationpart',
									'params' => array( 'language'),
                               		'functions' => array( 'read' ));

$ViewList['generatefiles'] = array( 'script' => 'generatefiles.php',
                                                      'default_navigation_part' => 'translatenavigationpart',
                                          'functions' => array( 'read' ));
                                          
                             		                               		
$ViewList['edit'] = array( 'script' => 'edit.php',
							'default_navigation_part' => 'translatenavigationpart',
                               		'functions' => array( 'read' ));                               		
                               		                             		                               		
$ViewList['notice'] = array( 'script' => 'notice.php',
                                          'default_navigation_part' => 'translatenavigationpart',
                                          'functions' => array( 'read' ));  
$FunctionList = array(); 
$FunctionList['read'] = array();


?>
